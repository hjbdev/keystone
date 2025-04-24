<?php

namespace App\Drivers;

use App\Models\Service;

abstract class DatabaseDriver extends Driver
{
    public ?string $containerName;

    public ?string $containerId;

    public ?array $credentials;

    abstract public function __construct(
        ?string $containerName = null,
        ?string $containerId = null,
        ?Service $service = null,
        ?array $credentials = null,
    );

    abstract public function defaultCredentials(): array;

    abstract public function createUser(string $user, string $password): string;

    // abstract public function createDatabase(string $db, string $user): string;
}
