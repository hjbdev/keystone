<?php

namespace App\Data\Deployments;

class Plan
{
    /**
     * @param Step[] $steps
     */
    public function __construct(
        public array $steps = [],
    ) {
        //
    }
}