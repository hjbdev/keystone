<?php

namespace App\Jobs\Services;

use App\Enums\DeploymentStatus;
use App\Enums\ServiceStatus;
use App\Models\Deployment;
use App\Models\Service;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeployService implements ShouldQueue
{
    use Queueable;

    protected Deployment $deployment;

    public function __construct(
        public Service $service,
    ) {
        //
    }

    public function handle(): void
    {
        $driver = $this->service->driver();
        $this->service->update([
            'status' => ServiceStatus::INSTALLING,
        ]);
        $this->deployment = $this->service->deployments()->create([
            'status' => DeploymentStatus::PENDING,
        ]);
        $deploymentPlan = $driver->getDeploymentPlan($this->deployment->hash);
        foreach ($deploymentPlan->steps as $index => $plannedStep) {
            $step = $this->deployment->steps()->create([
                'name' => $plannedStep->name,
                'order' => $index + 1,
                'status' => DeploymentStatus::PENDING,
                'script' => $plannedStep->getSafeScript(),
                'secrets' => $this->service->credentials,
            ]);
            if ($index === 0) {
                $step->dispatchJob();
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        if (isset($this->deployment)) {
            $this->deployment->update([
                'status' => DeploymentStatus::FAILED,
            ]);
            $this->service->update([
                'status' => ServiceStatus::ERROR,
            ]);
        }
    }
}
