<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Deployment extends Model
{
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function deployable(): MorphTo
    {
        return $this->morphTo();
    }
}
