<?php

namespace App\Enums;

enum ServerStatus: string
{
    case PENDING = 'pending';
    case PROVISIONING = 'provisioning';
    case UPDATING = 'updating';
    case ACTIVE = 'active';
    case DELETING = 'deleting';
    case DELETED = 'deleted';
}
