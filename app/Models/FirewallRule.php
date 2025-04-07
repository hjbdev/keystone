<?php

namespace App\Models;

use App\Actions\FirewallRules\InstallFirewallRule;
use App\Enums\FirewallRuleStatus;
use App\Enums\FirewallRuleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirewallRule extends Model
{
    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::created(function (self $firewallRule) {
            app(InstallFirewallRule::class)->execute($firewallRule);
        });
    }

    protected function casts(): array
    {
        return [
            'status' => FirewallRuleStatus::class,
            'type' => FirewallRuleType::class,
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function command(bool $delete = false): string
    {
        $command = 'ufw';

        if ($delete) {
            $command .= ' delete';
        }

        if ($this->type === 'allow') {
            $command .= ' allow';
        } elseif ($this->type === 'deny') {
            $command .= ' deny';
        }

        if ($this->from) {
            $command .= " from {$this->from}";
            $command .= ' to any port';
        }

        $command .= " {$this->ports}";

        return $command;
    }
}
