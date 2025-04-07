<?php

namespace App\Http\Integrations\Connectors;

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
            'Authorization' => 'Bearer '.config('services.hetzner.key'),
        ];
    }
}
