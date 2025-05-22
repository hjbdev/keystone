<?php

namespace App\Services\ServerProviders;

use App\Data\ServerProviders\CreatedServer;
use App\Data\ServerProviders\Network;
use App\Models\Provider;
use Illuminate\Support\Collection;
use Saloon\Http\Connector;

abstract class ServerProviderService
{
    protected Connector $connector;

    abstract public function createServer(
        string $name,
        string $serverType,
        string $location,
        string $image,
        string $networkId,
    ): CreatedServer;

    abstract public function forProvider(Provider $provider): static;

    abstract public function getServerTypes(): Collection;

    abstract public function getLocations(): Collection;

    abstract public function getImages(): Collection;

    abstract public function findNetwork(string $name, ?string $networkZone = null): ?Network;

    abstract public function createNetwork(string $name, ?string $networkZone = null): Network;
}
