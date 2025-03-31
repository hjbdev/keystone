<?php

namespace App\Jobs\Services;

use App\Drivers\Driver;
use App\Enums\DeploymentStatus;
use App\Enums\ServiceStatus;
use App\Models\Service;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeployService implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service,
        public ?string $defaultPassword = null,
    )
    {
        //
    }

    public function handle(): void
    {
        $driver = $this->service->driver($this->defaultPassword);
        /** @var \App\Models\Deployment $deployment */
        $deployment = $this->service->deployments()->create([
            'status' => DeploymentStatus::PENDING,
        ]);
        foreach ($driver->deploymentPlan->steps as $index => $plannedStep) {
            $step = $deployment->steps()->create([
                'order' => $index + 1,
                'status' => DeploymentStatus::PENDING,
                'script' => $plannedStep->getSafeScript(),
                'secrets' => [
                    'defaultPassword' => $this->defaultPassword,
                ],
            ]);
            if ($index === 0) {
                $step->dispatchJob();
            }
        }
    }
}
