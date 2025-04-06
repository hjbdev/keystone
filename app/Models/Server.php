<?php

namespace App\Models;

use App\Enums\ServerProvider;
use App\Enums\ServerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Ssh\Ssh;

class Server extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'provider' => ServerProvider::class,
            'status' => ServerStatus::class,
        ];
    }

    public function externalNetwork(): BelongsTo
    {
        return $this->belongsTo(Network::class, 'external_network_id');
    }
    
    public function internalNetwork(): BelongsTo
    {
        return $this->belongsTo(Network::class, 'internal_network_id');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function firewallRules(): HasMany
    {
        return $this->hasMany(FirewallRule::class);
    }

    public function sshClient(string $user = 'root'): Ssh
    {
        return Ssh::create($user, $this->ipv4)
            ->usePrivateKey(storage_path('app/private/ssh/id_ed25519'))
            ->disableStrictHostKeyChecking();
    }
}
