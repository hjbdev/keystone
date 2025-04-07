<?php

namespace App\Drivers\Postgres;

use App\Data\Deployments\Plan;
use App\Data\Deployments\PlannedStep as Step;
use App\Drivers\DatabaseDriver;

class Postgres17Driver extends DatabaseDriver
{
    public Plan $deploymentPlan;

    public string $defaultUser = 'keystone';

    public string $defaultDb = 'keystone';

    public function __construct(
        public ?string $containerName = null,
        public ?string $containerId = null,
        public ?string $defaultPassword = null,
    ) {
        $this->deploymentPlan = new Plan(steps: [
            new Step(
                name: 'Run the docker image',
                secrets: [
                    'defaultpassword' => $this->defaultPassword,
                ],
                script: function () {
                    $script = collect();
                    if ($this->containerName) {
                        $script->push('docker stop '.$this->containerName.' || true');
                    } elseif ($this->containerId) {
                        $script->push('docker stop '.$this->containerId.' || true');
                    }

                    $runCommand = 'docker run -d';
                    if ($this->containerName) {
                        $runCommand .= " --name {$this->containerName}";
                    }
                    if ($this->defaultPassword) {
                        $runCommand .= ' -e POSTGRES_PASSWORD=[!defaultPassword!]';
                    }
                    if ($this->defaultUser) {
                        $runCommand .= " -e POSTGRES_USER={$this->defaultUser}";
                    }
                    if ($this->defaultDb) {
                        $runCommand .= " -e POSTGRES_DB={$this->defaultDb}";
                    }

                    $runCommand .= ' -p 5432:5432 postgres:17';

                    return $runCommand;
                }
            ),
            new Step(
                name: 'Configure firewall',
                script: 'ufw allow 5432/tcp || true',
            ),
        ]);
    }
}
