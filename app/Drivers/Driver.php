<?php

namespace App\Drivers;

use App\Data\Deployments\Plan;
use App\Models\Service;

abstract class Driver
{
    public ?Service $service;

    public Plan $deploymentPlan;

    public ?string $containerName;

    public ?string $containerId;

    abstract public function __construct(
        ?string $containerName = null,
        ?string $containerId = null,
        ?Service $service = null,
    );
}
