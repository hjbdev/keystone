<?php

use App\Drivers\Postgres\Postgres17Driver;

return [
    'drivers' => [
        'postgres' => [
            '17' => Postgres17Driver::class,
        ]
    ]
];