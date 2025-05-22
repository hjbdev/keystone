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
        protected ?string $networkZone = null,
    ) {}

    protected function defaultBody(): array
    {
        $body = [
            'name' => $this->name,
            'ip_range' => '10.0.0.0/16',
        ];
        
        if ($this->networkZone) {
            $body['subnets'] = [
                [
                    'type' => 'cloud',
                    'ip_range' => '10.0.1.0/24',
                    'network_zone' => $this->networkZone
                ]
            ];
        }
        
        return $body;
    }

    public function resolveEndpoint(): string
    {
        return '/networks';
    }
}
