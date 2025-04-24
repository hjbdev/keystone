<?php

use App\Drivers\Caddy\Caddy2Driver;
use App\Drivers\Postgres\Postgres17Driver;
use App\Enums\ServiceCategory;
use App\Enums\ServiceType;

return [
    'drivers' => [
        'postgres' => [
            '17' => Postgres17Driver::class,
        ],
        'caddy' => [
            '2' => Caddy2Driver::class,
        ]
    ],
    'internal_ip_base' => env('INTERNAL_IP_BASE', '192.168.2.'),

    'services' => [
        ServiceCategory::DATABASE->value => [
            ServiceType::POSTGRES->value => [
                'name' => ServiceType::POSTGRES,
                'description' => 'PostgreSQL',
                'versions' => [
                    '17' => [
                        'name' => 'PostgreSQL 17',
                        'description' => 'PostgreSQL 17',
                        'image' => 'postgres:17',
                    ],
                ],
            ],
            ServiceType::MYSQL->value => [
                'name' => ServiceType::MYSQL,
                'description' => 'MySQL',
                'versions' => [
                    '9.0' => [
                        'name' => 'MySQL 9.2',
                        'description' => 'MySQL 9.2',
                        'image' => 'mysql:9.2'
                    ],
                ],
            ],
        ],
        ServiceCategory::GATEWAY->value => [
            ServiceType::NGINX->value => [
                'name' => ServiceType::NGINX,
                'description' => 'Nginx',
                'versions' => [
                    '1.27' => [
                        'name' => 'Nginx 1.27',
                        'description' => 'Nginx 1.27',
                        'image' => 'nginx:1.27',
                    ],
                ],
            ],
            ServiceType::CADDY->value => [
                'name' => ServiceType::CADDY,
                'description' => 'Caddy',
                'versions' => [
                    '2' => [
                        'name' => 'Caddy 2',
                        'description' => 'Caddy 2',
                        'image' => 'caddy:2'
                    ],
                ],
            ],
        ],
        ServiceCategory::APPLICATION->value => [
            ServiceType::PHP_FPM->value => [
                'name' => ServiceType::PHP_FPM,
                'description' => 'PHP-FPM',
                'versions' => [
                    '8.4' => [
                        'name' => 'PHP 8.4',
                        'description' => 'PHP 8.4',
                        'image' => 'serversideup/php:8.4-fpm-nginx',
                    ],
                ],
            ],
            ServiceType::FRANKENPHP->value => [
                'name' => ServiceType::FRANKENPHP,
                'description' => 'FrankenPHP',
                'versions' => [
                    '1.5' => [
                        'name' => 'FrankenPHP 1.5',
                        'description' => 'FrankenPHP 1.5',
                        'image' => 'dunglas/frankenphp:1.5-php8.4-bookworm',
                    ],
                ],
            ],
        ],
        ServiceCategory::CACHE->value => [
            ServiceType::REDIS->value => [
                'name' => ServiceType::REDIS,
                'description' => 'Redis',
                'versions' => [
                    '7.4' => [
                        'name' => 'Redis 7.4',
                        'description' => 'Redis 7.4',
                        'image' => 'redis:7.4',
                    ],
                ],
            ],
            ServiceType::VALKEY->value => [
                'name' => ServiceType::VALKEY,
                'description' => 'Valkey',
                'versions' => [
                    '8.1' => [
                        'name' => 'Valkey 8.1',
                        'description' => 'Valkey 8.1',
                        'image' => 'valkey/valkey:8.1',
                    ],
                ],
            ],
        ],
        ServiceCategory::STORAGE->value => [
        ],
    ]
];
