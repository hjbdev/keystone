<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Deployment extends Model
{
    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $deployment) {
            $deployment->hash = str()->random(16);
        });
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo('target');
    }
}
