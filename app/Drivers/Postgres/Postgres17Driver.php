<?php

namespace App\Drivers\Postgres;

use App\Data\Deployments\Plan;
use App\Data\Deployments\PlannedStep as Step;
use App\Drivers\DatabaseDriver;
use App\Enums\DeploymentStatus;
use App\Models\Service;
use Illuminate\Support\Str;

class Postgres17Driver extends DatabaseDriver
{
    public Plan $deploymentPlan;

    public function __construct(
        public ?string $containerName = null,
        public ?string $containerId = null,
        public ?Service $service = null,
        public ?array $credentials = null,
    ) {
        $credentials = $credentials ?? $this->defaultCredentials();
    }

    public function getDeploymentPlan(string $deploymentHash): Plan
    {
        $user = $credentials['user'] ?? null;
        $password = $credentials['password'] ?? null;
        $db = $credentials['db'] ?? null;

        if (!$user || !$password || !$db) {
            throw new \InvalidArgumentException('Missing required credentials');
        }

        $previousDeployment = $this->service?->deployments()
            ->where('status', DeploymentStatus::COMPLETED)
            ->first();

        return new Plan(steps: [
            new Step(
                name: 'Run the docker image',
                secrets: [
                    'password' => $password
                ],
                script: function () use ($user, $password, $db, $previousDeployment, $deploymentHash) {
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

                    $script->push($runCommand);

                    return $script->join(" && ");
                }
            ),
            new Step(
                name: 'Configure firewall', // @todo this should create a Firewallrule
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

    public function createUser(string $user, string $password): string
    {
        return "psql -U {$this->credentials['user']} -d {$this->credentials['db']} -c \"CREATE USER {$user} WITH PASSWORD '{$password}';\"";
    }
}
