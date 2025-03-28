<?php

namespace App\Jobs\Servers;

use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProvisionServer implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public function __construct(
        protected Server $server,
        protected string $rootPassword,
    )
    {
        //
    }

    public function handle(): void
    {
        //
    }
}
