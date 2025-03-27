<?php

namespace App\Models;

use App\Enums\ServerProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'provider' => ServerProvider::class,
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
