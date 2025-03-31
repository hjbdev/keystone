<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case NOT_INSTALLED = 'not-installed';
    case INSTALLING = 'installing';
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case ERROR = 'error';
    case UNKNOWN = 'unknown';
}