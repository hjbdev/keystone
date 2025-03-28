<?php

namespace App\Http\Integrations\Requests\Hetzner\Images;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetImagesRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected ?string $name = null,
        protected bool $includeDeprecated = false,
        protected ?string $architecture = null,
        protected ?string $labelSelector = null,
    ) {}

    protected function defaultQuery(): array
    {
        return [
            'name' => $this->name,
            'include_deprecated' => $this->includeDeprecated,
            'architecture' => $this->architecture,
            'label_selector' => $this->labelSelector,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/images';
    }
}
