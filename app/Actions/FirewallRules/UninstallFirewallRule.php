<?php

namespace App\Actions\FirewallRules;

use App\Enums\FirewallRuleStatus;
use App\Models\FirewallRule;
use Spatie\QueueableAction\QueueableAction;

class UninstallFirewallRule
{
    use QueueableAction;

    public function execute(
        FirewallRule $firewallRule,
    ) {
        $ssh = $firewallRule->server->sshClient();
        $result = $ssh->execute($firewallRule->command(true));

        if (! $result->isSuccessful()) {
            $firewallRule->update([
                'status' => FirewallRuleStatus::FAILED,
            ]);

            return;
        }

        $firewallRule->update([
            'status' => FirewallRuleStatus::REMOVED,
        ]);
    }
}
