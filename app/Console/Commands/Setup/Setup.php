<?php

namespace App\Console\Commands\Setup;

use Illuminate\Console\Command;

class Setup extends Command
{
    protected $signature = 'setup';

    protected $description = 'Initialize the application.';

    public function handle()
    {
        $this->call('migrate');
        $this->call(GenerateSshKey::class);
    }
}
