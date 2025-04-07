<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum ServiceStatus: string
{
    use Arrayable;

    case NOT_INSTALLED = 'not-installed';
    case INSTALLING = 'installing';
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case ERROR = 'error';
    case UNKNOWN = 'unknown';
}
