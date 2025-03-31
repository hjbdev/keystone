<?php

namespace App\Drivers;

use App\Data\Deployments\Plan;

abstract class Driver
{
    public Plan $deploymentPlan;
    public ?string $containerName;
    public ?string $containerId;

    abstract public function __construct(
        ?string $containerName = null,
        ?string $containerId = null,
    );
}