<?php

namespace App\Http\Integrations\Requests\Hetzner\Servers;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetNetworksRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::GET;

    public function __construct(
        protected ?string $name = null,
    ) {}

    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/networks';
    }
}
