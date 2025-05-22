<?php

namespace App\Data\ServerProviders;

class Network
{
    public function __construct(
        public string $id,
        public string $name,
        public string $ipRange,
        public ?string $networkZone = 'global',
    ) {}
}
