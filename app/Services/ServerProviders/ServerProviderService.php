<?php

namespace App\Services\ServerProviders;

use App\Data\ServerProviders\CreatedServer;
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
        string $rootPassword,
    ): CreatedServer;

    abstract public function getServerTypes(): Collection;

    abstract public function getLocations(): Collection;

    abstract public function getImages(): Collection;
}