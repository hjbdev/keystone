<?php

namespace App\Data\ServerProviders;

class Image
{
    public function __construct(
        public string $id,
        public string $name,
        public string $osFlavor,
        public string $osVersion,
    ) {}
}