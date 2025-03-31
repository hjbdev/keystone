<?php

namespace App\Jobs\Services;

use App\Enums\DeploymentStatus;
use App\Models\Step;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Ssh\Ssh;

class RunStep implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Step $step,
    )
    {
        //
    }

    public function handle(): void
    {
        $this->step->load('service.server');
        $this->step->update([
            'status' => DeploymentStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);

        $server = $this->step->service->server;

        $ssh = Ssh::create('root', $server->ipv4)
            ->usePrivateKey(storage_path('private/ssh/id_ed25519'))
            ->onOutput(function ($output) {
                $this->step->update([
                    'logs' => $this->step->logs . "\n" . trim($output),
                ]);
            });

        $ssh->execute($this->step->script);

        $this->step->update([
            'status' => DeploymentStatus::COMPLETED,
            'finished_at' => now(),
            'secrets' => null,
        ]);

        // Dispatch the next step if available
        if ($nextStep = $this->step->deployment->steps()->where('order', '>', $this->step->order)->orderBy('order', 'asc')->first()) {
            $nextStep->dispatchJob();
        }
    }
}
