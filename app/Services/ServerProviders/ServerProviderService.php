<?php

namespace App\Services\ServerProviders;

use Illuminate\Support\Collection;
use Saloon\Http\Connector;

interface ServerProviderService
{
    protected Connector $connector;

    public function createServer(
        string $name,
        string $serverType,
        string $location,
        string $image,
    ): bool;

    public function listServerTypes(): Collection;

    public function listLocations(): Collection;

    public function listImages(): Collection;
}