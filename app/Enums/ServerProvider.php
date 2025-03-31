<?php

namespace App\Enums;

enum ServerProvider: string
{
    case HETZNER = 'hetzner';
    case DIGITAL_OCEAN = 'digital-ocean';
}
