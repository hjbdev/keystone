<?php

namespace App\Models;

use App\Drivers\Driver;
use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => ServiceStatus::class,
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function slices(): HasMany
    {
        return $this->hasMany(Slice::class);
    }

    public function driver()//: Driver
    {
        // @todo. This is the class that controls the service
    }
}
