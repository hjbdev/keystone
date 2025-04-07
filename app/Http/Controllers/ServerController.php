<?php

namespace App\Http\Controllers;

use App\Actions\GenerateRandomSlug;
use App\Enums\NetworkType;
use App\Enums\ServerStatus;
use App\Jobs\Servers\WaitForServerToConnect;
use App\Models\Organisation;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        $organisation = Organisation::findOrFail($request->route('organisation'));

        $locations = null;
        $serverTypes = null;
        $images = null;

        if ($request->has('provider')) {
            $provider = Provider::findOrFail($request->provider);
            $providerService = $provider->service();

            if ($providerService) {
                $locations = Cache::remember($request->provider.'.locations', now()->addHour(), function () use ($providerService) {
                    return $providerService->getLocations();
                });
                $serverTypes = Cache::remember($request->provider.'.serverTypes', now()->addHour(), function () use ($providerService) {
                    return $providerService->getServerTypes();
                });
                $images = Cache::remember($request->provider.'.images', now()->addHour(), function () use ($providerService) {
                    return $providerService->getImages();
                });
            }
        }

        return inertia('servers/Create', [
            'providers' => $organisation->providers,
            'locations' => $locations,
            'serverTypes' => $serverTypes,
            'images' => $images,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider' => ['required', 'exists:providers,id'],
            'server_type' => ['required', 'string'],
            'location' => ['required', 'string'],
            'image' => ['required', 'string'],
        ]);

        $sudoPassword = Str::random(32);
        /** @var Provider $provider */
        $provider = Provider::findOrFail($request->provider);
        $providerService = $provider->service();

        if (! $providerService) {
            return back()->with('error', 'Invalid provider');
        }

        if (! $network = $provider->networks()->first()) {
            // we need a keystone network to create this server
            $createdNetwork = $providerService->createNetwork(
                name: 'keystone',
            );

            $network = $provider->networks()->create([
                'organisation_id' => $provider->organisation_id,
                'external_id' => $createdNetwork->id,
                'type' => NetworkType::EXTERNAL,
                'name' => $createdNetwork->name,
                'ip_range' => $createdNetwork->ipRange,
            ]);
        }

        $createdServer = $providerService->createServer(
            name: app(GenerateRandomSlug::class)->execute(), // @todo allow custom name
            serverType: $request->server_type,
            location: $request->location,
            image: $request->image,
            networkId: $network->external_id,
        );

        $organisation = Organisation::findOrFail($request->route('organisation'));

        $server = $organisation->servers()->create([
            'name' => $createdServer->name,
            'provider_id' => $provider->id,
            'external_id' => $createdServer->id,
            'ipv4' => $createdServer->ipv4,
            'ipv6' => $createdServer->ipv6,
            'private_ip' => $createdServer->privateIp,
            'provider_status' => $createdServer->status,
            'status' => ServerStatus::WAITING_FOR_PROVIDER,
            'region' => $request->location,
            'os' => $request->image,
            'plan' => $request->server_type,
            'user' => 'keystone',
            'external_network_id' => $network->id,
        ]);

        dispatch(new WaitForServerToConnect(
            server: $server,
            rootPassword: $createdServer->rootPassword,
            sudoPassword: $sudoPassword,
        ))->delay(now()->addSeconds(5));

        session()->flash('sudo_password', $sudoPassword);

        return redirect()->route('servers.show', ['organisation' => $organisation->id, 'server' => $server->id]);
    }

    public function show(Request $request)
    {
        $organisation = Organisation::findOrFail($request->route('organisation'));
        $server = $organisation->servers()->findOrFail($request->route('server'));

        return inertia('servers/Show', [
            'server' => $server->load('services.slices'),
        ]);
    }
}
