<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum FirewallRuleStatus: string
{
    use Arrayable;

    case UNINSTALLED = 'uninstalled';
    case INSTALLED = 'installed';
    case FAILED = 'failed';
    case REMOVED = 'removed';
}