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
}