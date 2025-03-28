<?php

namespace App\Http\Integrations\Requests\Hetzner\ServerTypes;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetServerTypesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/server_types';
    }
}