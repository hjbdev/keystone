<?php

namespace App\Actions;

use App\Services\ServerProviders\HetznerService;
use App\Services\ServerProviders\ServerProviderService;

class GetProviderService
{
    public function execute(string $provider): ServerProviderService|null
    {
        return match ($provider) {
            'hetzner' => new HetznerService(),
            default => null,
        };
    }
}
