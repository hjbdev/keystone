<?php

namespace App\Models;

use App\Enums\FirewallRuleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirewallRule extends Model
{
    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::created(function (self $firewallRule) {
            $firewallRule->install();
        });
    }

    protected function casts(): array
    {
        return [
            'status' => FirewallRuleStatus::class,
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function install(): void
    {
        $ssh = $this->server->sshClient();

        $command = "ufw";

        if ($this->type === 'allow') {
            $command .= " allow";
        } elseif ($this->type === 'deny') {
            $command .= " deny";
        }

        if ($this->from) {
            $command .= " from {$this->from}";
            $command .= " to any port";
        }

        $command .= " {$this->ports}";

        $result = $ssh->execute($command);

        if (! $result->isSuccessful()) {
            $this->update([
                'status' => FirewallRuleStatus::FAILED,
            ]);
            return;
        }
        $this->update([
            'status' => FirewallRuleStatus::APPLIED,
        ]);
    }

    public function remove(): void
    {
        $ssh = $this->server->sshClient();

        $command = "ufw";

        if ($this->type === 'allow') {
            $command .= " delete allow";
        } elseif ($this->type === 'deny') {
            $command .= " delete deny";
        }

        if ($this->from) {
            $command .= " from {$this->from}";
            $command .= " to any port";
        }

        $command .= " {$this->ports}";

        $result = $ssh->execute($command);

        if (! $result->isSuccessful()) {
            $this->update([
                'status' => FirewallRuleStatus::FAILED,
            ]);
            return;
        }
        $this->update([
            'status' => FirewallRuleStatus::REMOVED,
        ]);
    }
}
