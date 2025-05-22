<?php

namespace App\Services\ServerProviders;

use App\Data\ServerProviders\CreatedServer;
use App\Data\ServerProviders\Image;
use App\Data\ServerProviders\Location;
use App\Data\ServerProviders\Network;
use App\Data\ServerProviders\ServerType;
use App\Http\Integrations\Connectors\HetznerConnector;
use App\Http\Integrations\Requests\Hetzner\Images\GetImagesRequest;
use App\Http\Integrations\Requests\Hetzner\Locations\GetLocationsRequest;
use App\Http\Integrations\Requests\Hetzner\Networks\CreateNetworkRequest;
use App\Http\Integrations\Requests\Hetzner\Networks\GetNetworksRequest;
use App\Http\Integrations\Requests\Hetzner\Servers\CreateServerRequest;
use App\Http\Integrations\Requests\Hetzner\ServerTypes\GetServerTypesRequest;
use App\Models\Provider;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class HetznerService extends ServerProviderService
{
    protected Provider $provider;

    public function __construct()
    {
        $this->connector = new HetznerConnector;
    }

    public function forProvider(Provider $provider): static
    {
        $this->provider = $provider;
        $this->connector = $this->connector->withTokenAuth(
            $provider->token,
        );

        return $this;
    }

    public function createServer(
        string $name,
        string $serverType,
        string $location,
        string $image,
        string $networkId,
    ): CreatedServer {
        $response = $this->connector->send(new CreateServerRequest(
            image: $image,
            name: $name,
            serverType: $serverType,
            location: $location,
            networks: [
                $networkId,
            ],
        ));

        if ($response->status() !== 201) {
            Log::error('Failed to create server on Hetzner', [
                'response' => $response->json(),
                'status' => $response->status(),
                'name' => $name,
            ]);
            throw new Exception('Failed to create server on Hetzner');
        }

        return new CreatedServer(
            id: $response->json('server.id'),
            name: $name,
            rootPassword: $response->json('root_password'),
            status: $response->json('server.status'),
            ipv4: $response->json('server.public_net.ipv4.ip'),
            ipv6: $response->json('server.public_net.ipv6.ip'),
            networkId: $networkId,
            privateIp: $response->json('server.private_net.0.ip'),
        );
    }

    public function getServerTypes(): Collection
    {
        $response = $this->connector->send(new GetServerTypesRequest);

        if ($response->status() !== 200) {
            throw new Exception('Failed to fetch server types from Hetzner');
        }

        return collect($response->json('server_types'))->where('deprecated', false)->where('architecture', 'x86')->map(function ($serverType) {
            return new ServerType(
                id: $serverType['id'],
                name: $serverType['name'],
                cores: $serverType['cores'],
                memory: $serverType['memory'],
                disk: $serverType['disk'],
                priceMonthly: $serverType['prices'][0]['monthly']['gross'] ?? 0,
                priceHourly: $serverType['prices'][0]['hourly']['gross'] ?? 0,
            );
        })->values();
    }

    public function getLocations(): Collection
    {
        $response = $this->connector->send(new GetLocationsRequest);

        if ($response->status() !== 200) {
            throw new Exception('Failed to fetch locations from Hetzner');
        }

        return collect($response->json('locations'))->map(function ($location) {
            return new Location(
                id: $location['id'],
                name: $location['name'],
                country: $location['country'],
                city: $location['city'],
            );
        })->values();
    }

    public function getImages(): Collection
    {
        $response = $this->connector->send(new GetImagesRequest(
            architecture: 'x86',
        ));

        if ($response->status() !== 200) {
            throw new Exception('Failed to fetch images from Hetzner');
        }

        return collect($response->json('images'))->where('os_flavor', 'ubuntu')->map(function ($image) {
            return new Image(
                id: $image['id'],
                name: $image['description'],
                osFlavor: $image['os_flavor'],
                osVersion: $image['os_version'],
            );
        })->where('osVersion', '!=', 'unknown')->values();
    }

    public function findNetwork(string $name): ?Network
    {
        $response = $this->connector->send(new GetNetworksRequest(
            name: $name,
        ));

        if ($response->status() !== 200) {
            throw new Exception('Failed to fetch networks from Hetzner');
        }

        $network = collect($response->json('networks'))->where('name', $name)->first();

        if ($network) {
            return new Network(
                id: $network['id'],
                name: $network['name'],
                ipRange: $network['ip_range'],
            );
        }

        return null;
    }

    public function createNetwork(string $name): Network
    {
        $response = $this->connector->send(new CreateNetworkRequest(
            name: $name,
        ));

        if ($response->status() !== 201) {
            Log::error('Failed to create network on Hetzner', [
                'response' => $response->json(),
                'status' => $response->status(),
                'name' => $name,
            ]);
            throw new Exception('Failed to create network on Hetzner');
        }

        return new Network(
            id: $response->json('network.id'),
            name: $response->json('network.name'),
            ipRange: $response->json('network.ip_range'),
        );
    }
}
