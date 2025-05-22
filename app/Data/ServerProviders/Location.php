<?php

namespace App\Data\ServerProviders;

class Location
{
    public function __construct(
        public string $id,
        public string $name,
        public string $country,
        public string $city,
        public ?string $networkZone = null,
    ) {}
}
