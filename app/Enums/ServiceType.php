<?php

namespace App\Enums;

enum ServiceType: string {
    case FRANKENPHP = 'frankenphp';
    case PHP_FPM = 'php-fpm';
    case POSTGRES = 'postgres';
    case CADDY = 'caddy';
    case VALKEY = 'valkey';

    // future?
    case MYSQL = 'mysql';
    case NGINX = 'nginx';
    case REDIS = 'redis';
}
