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
            'status' => ServerStatus::class,
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $server) {
            $existingServer = Server::whereOrganisationId($server->organisation_id)
                ->orderByDesc('internal_ip_ending')
                ->first();

            $server->internal_ip_ending = $existingServer
                ? $existingServer->internal_ip_ending + 1
                : 2;
            $server->internal_ip = config('keystone.internal_ip_base').$server->internal_ip_ending;
        });
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

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function sshClient(string $user = 'root'): Ssh
    {
        return Ssh::create($user, $this->ipv4)
            ->usePrivateKey(storage_path('app/private/ssh/id_ed25519'))
            ->disableStrictHostKeyChecking();
    }
}
