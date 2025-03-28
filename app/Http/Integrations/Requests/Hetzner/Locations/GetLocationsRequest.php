<?php

namespace App\Http\Integrations\Requests\Hetzner\Locations;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetLocationsRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/locations';
    }
}
