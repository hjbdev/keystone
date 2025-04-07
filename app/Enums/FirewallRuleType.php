<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum FirewallRuleType: string
{
    use Arrayable;

    case ALLOW = 'allow';
    case DENY = 'deny';
}
