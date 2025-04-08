<?php

namespace App\Models;

use App\Enums\ProviderType;
use App\Services\ServerProviders\HetznerService;
use App\Services\ServerProviders\ServerProviderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    /** @use HasFactory<\Database\Factories\ProviderFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['token'];

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
            ProviderType::HETZNER => app(HetznerService::class)->forProvider($this),
            default => null,
        };
    }
}
