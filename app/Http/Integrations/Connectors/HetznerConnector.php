<?php

namespace App\Http\Integrations\Connectors;

use App\Models\Provider;
use Saloon\Http\Connector;

class HetznerConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.hetzner.cloud/v1';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
