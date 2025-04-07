<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum FirewallRuleStatus: string
{
    use Arrayable;

    case NOT_INSTALLED = 'not-installed';
    case INSTALLED = 'installed';
    case FAILED = 'failed';
    case REMOVED = 'removed';
}
