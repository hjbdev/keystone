<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum OrganisationRole: string
{
    use Arrayable;

    case ADMIN = 'admin';
    case MEMBER = 'member';
}
