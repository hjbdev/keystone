<?php

namespace App\Drivers;

use App\Models\Service;

abstract class GatewayDriver extends Driver
{
    public ?string $containerName;

    public ?string $containerId;

    abstract public function __construct(
        ?string $containerName = null,
        ?string $containerId = null,
        ?Service $service = null,
    );
}
