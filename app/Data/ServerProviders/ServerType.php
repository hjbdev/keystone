<?php

namespace App\Data\ServerProviders;

class ServerType
{
    /**
     * @param string $name The name of the server type
     * @param int $cores The number of cores
     * @param int $memory The amount of memory in GB
     * @param int $disk The amount of disk space in GB
     */
    public function __construct(
        public string $id,
        public string $name,
        public int $cores,
        public int $memory,
        public int $disk,
        public float $priceMonthly,
        public float $priceHourly,
    ) {}
}