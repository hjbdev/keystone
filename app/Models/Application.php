<?php

namespace App\Models;

use App\Enums\RepositoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'repository_type' => RepositoryType::class,
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function environments(): HasMany
    {
        return $this->hasMany(Environment::class);
    }
}
