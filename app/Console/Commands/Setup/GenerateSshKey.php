<?php

namespace App\Console\Commands\Setup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class GenerateSshKey extends Command
{
    protected $signature = 'setup:generate-ssh-key';

    protected $description = 'Generates an SSH key pair for the application.';

    public function handle()
    {
        if (file_exists(storage_path('app/private/ssh/id_ed25519'))) {
            $this->components->info('SSH key pair already exists.');

            return;
        }

        $this->components->info('Generating SSH key pair...');
        if (! file_exists(storage_path('app/private/ssh'))) {
            $this->components->info('ssh directory does not exist. Creating it now...');
            mkdir(storage_path('app/private/ssh'), 0755, true);
        }

        $result = Process::run(['ssh-keygen', '-t', 'ed25519', '-f', storage_path('app/private/ssh/id_ed25519'), '-N', '']);

        if (! $result->successful()) {
            $this->components->error('Failed to generate SSH key pair.');
            $this->line($result->output());
            $this->line($result->errorOutput());

            return;
        }

        $this->components->success('SSH key pair generated successfully.');
    }
}
