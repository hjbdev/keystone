<?php

use App\Drivers\Postgres\Postgres17Driver;

return [
    'drivers' => [
        'postgres' => [
            '17' => Postgres17Driver::class,
        ],
    ],
    'internal_ip_base' => env('INTERNAL_IP_BASE', '192.168.2.'),
];
