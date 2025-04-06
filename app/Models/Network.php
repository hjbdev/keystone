<?php

namespace App\Models;

use App\Enums\NetworkType;
use App\Enums\ServerProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Network extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => NetworkType::class,
            'provider' => ServerProvider::class,
        ];
    }

    public function internalServers(): HasMany
    {
        return $this->hasMany(Server::class, 'internal_network_id');
    }

    public function externalServers(): HasMany
    {
        return $this->hasMany(Server::class, 'external_network_id');
    }
    
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
