<?php

namespace App\Jobs\Services;

use App\Enums\DeploymentStatus;
use App\Enums\ServiceStatus;
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
        $this->step->load('deployment.target.server');
        $this->step->update([
            'status' => DeploymentStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);

        $server = $this->step->deployment->target->server;

        $ssh = Ssh::create('root', $server->ipv4)
            ->usePrivateKey(storage_path('app/private/ssh/id_ed25519'))
            ->disableStrictHostKeyChecking()
            ->onOutput(function ($output) {
                $this->step->update([
                    'logs' => $this->step->logs . "\n" . trim($output),
                ]);
            });

        $result = $ssh->execute($this->step->script);

        if (! $result->isSuccessful()) {
            $this->step->update([
                'status' => DeploymentStatus::FAILED,
                'finished_at' => now(),
                'logs' => $this->step->logs . "\n" . trim($result->getErrorOutput()),
            ]);

            return;
        }

        $this->step->update([
            'status' => DeploymentStatus::COMPLETED,
            'finished_at' => now(),
            'secrets' => null,
        ]);

        // Dispatch the next step if available
        if ($nextStep = $this->step->deployment->steps()->where('order', '>', $this->step->order)->orderBy('order', 'asc')->first()) {
            $nextStep->dispatchJob();
        } else {
            $this->step->deployment->update([
                'status' => DeploymentStatus::COMPLETED,
                'finished_at' => now(),
            ]);
            $this->step->deployment->target->update([
                'status' => ServiceStatus::RUNNING,
            ]);
        }
    }
}
