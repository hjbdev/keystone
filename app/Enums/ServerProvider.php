<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum ServerProvider: string
{
    use Arrayable;

    case HETZNER = 'hetzner';
    case DIGITAL_OCEAN = 'digital-ocean';
}
