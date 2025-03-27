<?php

namespace App\Enums;

enum ServerProvider: string
{
    case Hetzner = 'Hetzner';
    case DigitalOcean = 'DigitalOcean';
}
