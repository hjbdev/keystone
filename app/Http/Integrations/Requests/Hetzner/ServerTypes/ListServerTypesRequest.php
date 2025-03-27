<?php

namespace App\Http\Integrations\Requests\Hetzner\ServerTypes;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListServerTypesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/server_types';
    }
}