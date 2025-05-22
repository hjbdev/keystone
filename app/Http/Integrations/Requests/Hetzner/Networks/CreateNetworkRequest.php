<?php

namespace App\Http\Integrations\Requests\Hetzner\Networks;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateNetworkRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected ?string $name = null,
    ) {}

    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
            'ip_range' => '10.0.0.0/16',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/networks';
    }
}
