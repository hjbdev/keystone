<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum DeploymentStatus: string
{
    use Arrayable;
    
    case PENDING = 'pending';
    case IN_PROGRESS = 'in-progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'canceled';
    case FAILED = 'failed';
}