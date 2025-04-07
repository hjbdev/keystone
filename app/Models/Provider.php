<?php

namespace App\Models;

use App\Enums\ProviderType;
use App\Services\ServerProviders\HetznerService;
use App\Services\ServerProviders\ServerProviderService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
            'type' => ProviderType::class,
        ];
    }

    public function networks(): HasMany
    {
        return $this->hasMany(Network::class);
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    public function service(): ?ServerProviderService
    {
        return match ($this->type) {
            ProviderType::HETZNER => new HetznerService($this),
            default => null,
        };
    }
}
