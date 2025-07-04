<?php

namespace App\Drivers\Caddy;

use App\Drivers\GatewayDriver;
use App\Data\Deployments\Plan;
use App\Data\Deployments\PlannedStep as Step;
use App\Enums\DeploymentStatus;
use App\Models\Service;

class Caddy2Driver extends GatewayDriver
{
    public ?string $containerName;
    public ?string $containerId;

    public function __construct(
        ?string $containerName = null,
        ?string $containerId = null,
        public ?Service $service = null,
    ) {
        $this->containerName = $containerName;
        $this->containerId = $containerId;
        $this->service = $service;
    }

    public function getDeploymentPlan(string $deploymentHash): Plan
    {
        $previousDeployment = $this->service?->deployments()
            ->where('status', DeploymentStatus::COMPLETED)
            ->first();

        return new Plan(steps: [
            new Step(
                name: 'Generate Caddyfile',
                script: function () {
                    $script = collect();
                    $script->push('cd ~');
                    $script->push('test -d services || mkdir services');
                    $script->push('cd services');
                    $script->push("test -d {$this->service->id} || mkdir {$this->service->id}");
                    $script->push("cd {$this->service->id}");
                    return $script->join("\n");
                }
            ),
            new Step(
                name: 'Run the docker image',
                script: function () use ($previousDeployment, $deploymentHash) {
                    $script = collect();
                    if ($this->containerName && $previousDeployment) {
                        $script->push("docker stop \"{$this->containerName}-{$previousDeployment->hash}\" || true");
                    } elseif ($this->containerId) {
                        $script->push('docker stop ' . $this->containerId . ' || true');
                    }

                    $runCommand = 'docker run -d';
                    if ($this->containerName) {
                        $runCommand .= " --name \"{$this->containerName}-{$deploymentHash}\"";
                    }
                    $runCommand .= ' -p 80:80 -p 443:443 caddy:2';

                    $script->push($runCommand);

                    return $script->join(" && ");
                }
            ),
        ]);
    }

    public function buildCaddyfile(): string
    {
        $caddyfile = "http://{$this->service->name} {\n";
        $caddyfile .= "    reverse_proxy {$this->service->credentials['backend']}\n";
        $caddyfile .= "}\n";

        return $caddyfile;
    }
}
