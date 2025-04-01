<?php

namespace App\Enums;

use App\Enums\Concerns\Arrayable;

enum RepositoryType: string
{
    use Arrayable;

    case GIT = 'git';
}