<?php

namespace App\Drivers;

abstract class DatabaseDriver extends Driver
{
    public ?string $containerName;

    public ?string $containerId;

    public ?array $credentials;

    abstract public function __construct(
        ?string $containerName = null,
        ?string $containerId = null,
        ?array $credentials = null,
    );
}
