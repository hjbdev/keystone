<?php

namespace App\Models;

use App\Enums\OrganisationRole;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class OrganisationUser extends MorphPivot
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'role' => OrganisationRole::class,
        ];
    }
}
