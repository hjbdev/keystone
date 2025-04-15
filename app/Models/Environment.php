<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Environment extends Model
{
    protected $guarded = [];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function slices(): HasMany
    {
        return $this->hasMany(Slice::class);
    }

    public function services(): HasManyThrough
    {
        return $this->hasManyThrough(Service::class, Slice::class);
    }
}
