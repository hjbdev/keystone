<?php

namespace App\Enums;

enum ServerStatus: string
{
    case WAITING_FOR_PROVIDER = 'waiting-for-provider';
    case PROVIDER_TIMEOUT = 'provider-timeout';
    case UNPROVISIONED = 'unprovisioned';
    case PROVISIONING = 'provisioning';
    case PROVISIONING_FAILED = 'provisioning-failed';
    case UPDATING = 'updating';
    case ACTIVE = 'active';
    case DELETING = 'deleting';
    case DELETED = 'deleted';
}
