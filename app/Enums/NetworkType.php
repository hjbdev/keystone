<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum NetworkType: string
{
    use Arrayable;

    case EXTERNAL = 'external'; // managed by provider
    case INTERNAL = 'internal'; // managed by keystone
}
