<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum ProviderType: string
{
    use Arrayable;

    case HETZNER = 'hetzner';
    case DIGITAL_OCEAN = 'digital-ocean';
}
