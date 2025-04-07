<?php

namespace App\Data\ServerProviders;

class CreatedServer
{
    public function __construct(
        public string $name,
        public string $rootPassword,
        public string $id,
        public string $status,
        public string $ipv4,
        public string $ipv6,
        public string $privateIp,
        public string $networkId,
    ) {}
}
