<?php

namespace App\Drivers;

use App\Data\Deployments\Plan;

interface Driver
{
    public Plan $deploymentPlan { get; }

    public function __construct(
        public ?string $containerName = null,
        public ?string $containerId = null,
    );
}