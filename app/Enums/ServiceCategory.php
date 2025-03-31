<?php

namespace App\Enums;

enum ServiceCategory: string
{
    case DATABASE = 'database';
    case APPLICATION = 'application';
    case GATEWAY = 'gateway';
    case STORAGE = 'storage';
    case CACHE = 'cache';
}