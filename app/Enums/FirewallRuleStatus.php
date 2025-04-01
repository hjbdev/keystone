<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum FirewallRuleStatus: string
{
    use Arrayable;

    case NOT_APPLIED = 'not-applied';
    case APPLIED = 'applied';
    case FAILED = 'failed';
}