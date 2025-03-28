<?php

namespace App\Services\ServerProviders;

use App\Data\ServerProviders\Image;
use App\Data\ServerProviders\Location;
use App\Data\ServerProviders\ServerType;
use App\Http\Integrations\Connectors\HetznerConnector;
use App\Http\Integrations\Requests\Hetzner\Images\ListImagesRequest;
use App\Http\Integrations\Requests\Hetzner\Locations\ListLocationsRequest;
use App\Http\Integrations\Requests\Hetzner\ServerTypes\ListServerTypesRequest;
use Exception;
use Illuminate\Support\Collection;

class HetznerService implements ServerProviderService
{
    public function __construct()
    {
        $this->connector = new HetznerConnector;
    }

    public function createServer(
        string $name,
        string $serverType,
        string $location,
        string $image,
    ): bool {
        return false;
    }

    public function listServerTypes(): Collection
    {
        $response = $this->connector->send(new ListServerTypesRequest);

        if ($response->status() !== 200) {
            throw new Exception('Failed to fetch server types from Hetzner');
        }

        return collect($response->json('server_types'))->where('deprecated', false)->where('architecture', 'x86')->map(function ($serverType) {
            return new ServerType(
                id: $serverType['id'],
                name: $serverType['name'],
                cores: $serverType['cores'],
                memory: $serverType['memory'] * 1024,
                disk: $serverType['disk'],
                priceMonthly: $serverType['prices'][0]['monthly']['gross'] ?? 0,
                priceHourly: $serverType['prices'][0]['hourly']['gross'] ?? 0,
            );
        });
    }

    public function listLocations(): Collection
    {
        $response = $this->connector->send(new ListLocationsRequest);

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
        });
    }

    public function listImages(): Collection
    {
        $response = $this->connector->send(new ListImagesRequest(
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
        });
    }
}
