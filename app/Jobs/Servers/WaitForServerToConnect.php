<?php

namespace App\Jobs\Servers;

use App\Enums\ServerStatus;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Ssh\Ssh;

class WaitForServerToConnect implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public int $retryAfter = 15;
    public int $tries = 40;

    public function __construct(
        protected Server $server,
        protected string $rootPassword,
        protected string $sudoPassword,
    )
    {
        //
    }

    public function handle(): void
    {
        try {
            Ssh::create('root', $this->server->ipv4 ?? $this->server->ipv6)
                ->usePassword($this->rootPassword)
                ->setTimeout(10)
                ->execute('echo "Connected"');

            $this->server->update([
                'status' => ServerStatus::UNPROVISIONED,
            ]);

            dispatch(new ProvisionServer($this->server, $this->rootPassword, $this->sudoPassword));
        } catch (\Throwable $e) {
            return;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->server->update([
            'status' => ServerStatus::PROVIDER_TIMEOUT,
        ]);
    }
}
