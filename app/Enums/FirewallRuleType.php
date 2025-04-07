<?php

namespace App\Enums;

enum FirewallRuleType: string
{
    case ALLOW = 'allow';
    case DENY = 'deny';
}