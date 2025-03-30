<?php

namespace App\Enums;

enum ServerProvider: string
{
    case Hetzner = 'hetzner';
    case DigitalOcean = 'digital-ocean';
}
