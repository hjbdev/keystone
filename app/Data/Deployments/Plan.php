<?php

namespace App\Data\Deployments;

class Plan
{
    /**
     * @param  PlannedStep[]  $steps
     */
    public function __construct(
        public array $steps = [],
    ) {
        //
    }
}
