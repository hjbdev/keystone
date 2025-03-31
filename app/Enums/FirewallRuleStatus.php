<?php

namespace App\Enums;

enum FirewallRuleStatus: string
{
    case NOT_APPLIED = 'not-applied';
    case APPLIED = 'applied';
    case FAILED = 'failed';
}