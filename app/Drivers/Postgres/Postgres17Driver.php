<?php

namespace App\Drivers\Postgres;

use App\Data\Deployments\Plan;
use App\Data\Deployments\PlannedStep as Step;
use App\Drivers\DatabaseDriver;
use Illuminate\Support\Str;

class Postgres17Driver extends DatabaseDriver
{
    public Plan $deploymentPlan;

    public function __construct(
        public ?string $containerName = null,
        public ?string $containerId = null,
        public ?array $credentials = null,
    ) {
        $user = $credentials['user'];
        $password = $credentials['password'];
        $db = $credentials['db'];

        $this->deploymentPlan = new Plan(steps: [
            new Step(
                name: 'Run the docker image',
                secrets: [
                    'password' => $password
                ],
                script: function () use ($user, $password, $db) {
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
                    if ($password) {
                        $runCommand .= ' -e POSTGRES_PASSWORD=[!password!]';
                    }
                    if ($user) {
                        $runCommand .= " -e POSTGRES_USER={$user}";
                    }
                    if ($db) {
                        $runCommand .= " -e POSTGRES_DB={$db}";
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

    public function defaultCredentials(): array
    {
        return [
            'password' => Str::random(16),
            'user' => 'keystone',
            'db' => 'keystone',
        ];
    }
}
