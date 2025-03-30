<?php

namespace App\Http\Controllers;

use App\Actions\GenerateRandomSlug;
use App\Actions\GetProviderService;
use App\Enums\ServerProvider;
use App\Enums\ServerStatus;
use App\Jobs\Servers\WaitForServerToConnect;
use App\Models\Organisation;
use App\Services\ServerProviders\HetznerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NunoMaduro\Collision\Provider;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        $organisation = Organisation::findOrFail($request->route('organisation'));

        return inertia('servers/Index', [
            'servers' => $organisation->servers()->paginate(30),
        ]);
    }

    public function create(Request $request)
    {
        $locations = null;
        $serverTypes = null;
        $images = null;

        if ($request->has('provider')) {
            $providerService = app(GetProviderService::class)->execute($request->provider);

            if ($providerService) {
                $locations = Cache::remember($request->provider . '.locations', now()->addHour(), function () use ($providerService) {
                    return $providerService->getLocations();
                });
                $serverTypes = Cache::remember($request->provider . '.serverTypes', now()->addHour(), function () use ($providerService) {
                    return $providerService->getServerTypes();
                });
                $images = Cache::remember($request->provider . '.images', now()->addHour(), function () use ($providerService) {
                    return $providerService->getImages();
                });
            }
        }

        return inertia('servers/Create', [
            'locations' => $locations,
            'serverTypes' => $serverTypes,
            'images' => $images,
        ]);
    }

    public function store(Request $request)
    {
        $sudoPassword = Str::random(32);
        $providerService = app(GetProviderService::class)->execute($request->provider);

        if (!$providerService) {
            return back()->with('error', 'Invalid provider');
        }

        $createdServer = $providerService->createServer(
            name: app(GenerateRandomSlug::class)->execute(), // @todo allow custom name
            serverType: $request->server_type,
            location: $request->location,
            image: $request->image,
        );

        $organisation = Organisation::findOrFail($request->route('organisation'));

        $server = $organisation->servers()->create([
            'name' => $createdServer->name,
            'provider' => ServerProvider::tryFrom($request->provider),
            'provider_id' => $createdServer->id,
            'ipv4' => $createdServer->ipv4,
            'ipv6' => $createdServer->ipv6,
            'provider_status' => $createdServer->status,
            'status' => ServerStatus::WAITING_FOR_PROVIDER,
            'region' => $request->location,
            'os' => $request->image,
            'plan' => $request->server_type,
            'user' => '',
        ]);

        dispatch(new WaitForServerToConnect(
            server: $server,
            rootPassword: $createdServer->rootPassword,
            sudoPassword: $sudoPassword,
        ))->delay(now()->addSeconds(5));

        logger('server created');
        logger($createdServer->id);
        logger($createdServer->ipv4);
        logger($createdServer->ipv6);
        logger($createdServer->rootPassword);
        logger($sudoPassword);

        session()->flash('sudo_password', $sudoPassword);

        return redirect()->route('servers.show', ['organisation' => $organisation->id, 'server' => $server->id]);
    }

    public function show(Request $request)
    {
        $organisation = Organisation::findOrFail($request->route('organisation'));
        $server = $organisation->servers()->findOrFail($request->route('server'));

        return inertia('servers/Show', [
            'server' => $server,
        ]);
    }
}
