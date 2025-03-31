<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case ERROR = 'error';
    case UNKNOWN = 'unknown';
}