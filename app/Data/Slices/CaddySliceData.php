<?php

namespace App\Data\Slices;

class CaddySliceData
{
    public function __construct(
        public string $domain,
        public string $type,
        public array $targets
    ) {}
}