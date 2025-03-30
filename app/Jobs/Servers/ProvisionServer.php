<?php

namespace App\Jobs\Servers;

use App\Enums\ServerStatus;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Ssh\Ssh;

class ProvisionServer implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public function __construct(
        protected Server $server,
        protected string $rootPassword,
        protected string $sudoPassword,
    ) {
        //
    }

    public function handle(): void
    {
        $ssh = Ssh::create('root', $this->server->ipv4 ?? $this->server->ipv6)
            ->usePassword($this->rootPassword)
            ->setTimeout(10);

        $provisionScriptUrl = route('provision-script', [
            'sudo_password' => $this->sudoPassword,
            'hostname' => str($this->server->name)->slug()->toString(),
            'server_id' => $this->server->id,
        ]);

        // Download the provision script and execute it
        // The script will run in the background
        $ssh->execute("wget --quiet --output-document=provision.sh \"{$provisionScriptUrl}\" && chmod +x provision.sh && ./provision.sh &");
        logger('executing script on server');
        logger("wget --quiet --output-document=provision.sh \"{$provisionScriptUrl}\" && chmod +x provision.sh && ./provision.sh &");

        $this->server->update([
            'status' => ServerStatus::PROVISIONING,
        ]);
    }
}
