<?php

namespace App\Http\Integrations\Requests\Hetzner\Servers;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected ?string $image = null,
        protected ?string $name = null,
        protected ?string $serverType = null,
        protected ?string $location = null,
    ) {}

    protected function defaultBody(): array
    {
        return [
            'image' => $this->image,
            'name' => $this->name,
            'server_type' => $this->serverType,
            'location' => $this->location,
            'user_data' => file_get_contents(resource_path('scripts/hetzner-cloudinit.yml')),
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/servers';
    }
}
