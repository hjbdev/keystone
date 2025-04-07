<?php

namespace App\Http\Controllers;

use App\Actions\Servers\SyncWireguardRules;
use App\Enums\ServerStatus;
use App\Events\Servers\ServerProvisioned;
use App\Models\Server;
use App\Support\Ip;
use Illuminate\Http\Request;

class ProvisionCallback extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'server_id' => ['required', 'integer', 'exists:servers,id'],
            'internal_public_key' => ['required', 'string'],
        ]);

        $server = Server::find($validated['server_id']);

        // Check against ipv4 and ipv6
        $isValidIp = false;
        if ($server->ipv4 && Ip::inNetwork($request->ip(), $server->ipv4)) {
            $isValidIp = true;
        }
        if ($server->ipv6 && Ip::inNetwork($request->ip(), $server->ipv6)) {
            $isValidIp = true;
        }

        if (! $isValidIp) {
            logger('someone tried to callback from an invalid IP');
            logger(' server ip: '.$server->ipv4);
            logger(' server ipv6: '.$server->ipv6);
            logger(' callback ip: '.$request->ip());
            logger(' server id: '.$server->id);

            return response('Unauthorized', 401);
        }

        $server->update([
            'status' => ServerStatus::ACTIVE,
            'internal_public_key' => $validated['internal_public_key'],
        ]);

        $server->organisation->servers()->each(function ($s) {
            app(SyncWireguardRules::class)->onQueue()->execute($s);
        });

        event(new ServerProvisioned($server));

        return response('OK', 200);
    }
}
