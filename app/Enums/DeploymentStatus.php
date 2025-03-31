<?php

namespace App\Enums;

enum DeploymentStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in-progress';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}