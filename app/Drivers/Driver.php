<?php

namespace App\Drivers;

use App\Data\Deployments\Plan;

interface Driver
{
    public Plan $deploymentPlan { get; }
}