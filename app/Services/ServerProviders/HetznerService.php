<?php

namespace App\Services\ServerProviders;

use App\Data\ServerProviders\CreatedServer;
use App\Data\ServerProviders\Image;
use App\Data\ServerProviders\Location;
use App\Data\ServerProviders\ServerType;
use App\Http\Integrations\Connectors\HetznerConnector;
use App\Http\Integrations\Requests\Hetzner\Images\GetImagesRequest;
use App\Http\Integrations\Requests\Hetzner\Locations\GetLocationsRequest;
use App\Http\Integrations\Requests\Hetzner\Servers\CreateServerRequest;
use App\Http\Integrations\Requests\Hetzner\ServerTypes\GetServerTypesRequest;
use App\Models\Provider;
use Exception;
use Illuminate\Support\Collection;

class HetznerService extends ServerProviderService
{
    public function __construct(Provider $provider)
    {
        $this->connector = new HetznerConnector($provider);
    }

    public function createServer(
        string $name,
        string $serverType,
        string $location,
        string $image,
    ): CreatedServer {
        $response = $this->connector->send(new CreateServerRequest(
            image: $image,
            name: $name,
            serverType: $serverType,
            location: $location,
        ));

        if ($response->status() !== 201) {
            throw new Exception('Failed to create server on Hetzner');
        }

        return new CreatedServer(
            id: $response->json('server.id'),
            name: $name,
            rootPassword: $response->json('root_password'),
            status: $response->json('server.status'),
            ipv4: $response->json('server.public_net.ipv4.ip'),
            ipv6: $response->json('server.public_net.ipv6.ip'),
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
}
