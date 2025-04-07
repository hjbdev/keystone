<?php

namespace App\Actions\Servers;

use App\Models\Server;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class SyncWireguardRules
{
    use QueueableAction;

    public function execute(
        Server $server,
    ) {
        $ssh = $server->sshClient();
        $result = $ssh->execute('wg show wg0');

        if (! $result->isSuccessful()) {
            logger()->error('Failed to retrieve WireGuard rules', [
                'server_id' => $server->id,
                'error' => $result->getErrorOutput(),
            ]);
            throw new \Exception('Failed to retrieve WireGuard rules');
        }

        $output = $result->getOutput();
        $commands = collect();

        $server->organisation->servers()->where('id', '!=', $server->id)->each(function ($organisationServer) use (&$commands, $output) {
            if (Str::contains($output, $organisationServer->internal_public_key)) {
                $commands->push("wg set wg0 peer {$organisationServer->internal_public_key} remove");
            }
            $commands->push("wg set wg0 peer {$organisationServer->internal_public_key} allowed-ips {$organisationServer->internal_ip}/32");
        });

        $result = $ssh->execute($commands->toArray());

        if (! $result->isSuccessful()) {
            logger()->error('Failed to sync WireGuard rules', [
                'server_id' => $server->id,
                'error' => $result->getErrorOutput(),
            ]);
            throw new \Exception('Failed to sync WireGuard rules');
        }

        logger()->info('Successfully synced WireGuard rules', [
            'server_id' => $server->id,
            'commands' => $commands->toArray(),
            'output' => $result->getOutput(),
        ]);
    }
}
