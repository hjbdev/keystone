<?php

namespace App\Models;

use App\Enums\ServerProvider;
use App\Enums\ServerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'provider' => ServerProvider::class,
            'status' => ServerStatus::class,
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
