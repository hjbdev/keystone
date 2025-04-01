<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum ServiceCategory: string
{
    use Arrayable;
    
    case DATABASE = 'database';
    case APPLICATION = 'application';
    case GATEWAY = 'gateway';
    case STORAGE = 'storage';
    case CACHE = 'cache';

    public static function getDescription(ServiceCategory|string $category) {
        if (is_string($category)) {
            $category = ServiceCategory::from($category);
        }
        if (! $category instanceof ServiceCategory) {
            throw new \InvalidArgumentException('Invalid category provided');
        }
        return match ($category) {
            self::APPLICATION => 'The base container image for your application',
            self::DATABASE => 'Postgres or MySQL',
            self::GATEWAY => 'The gateway is the first point of contact for your application',
            self::STORAGE => 'S3 or S3-compatible service',
            self::CACHE => 'Redis, Memcached or similar',
        };
    }
}