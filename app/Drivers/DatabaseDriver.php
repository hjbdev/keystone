<?php

namespace App\Drivers;

abstract class DatabaseDriver extends Driver
{
    public string $defaultUser = 'keystone';
    public string $defaultDb = 'keystone';
    public ?string $containerName;
    public ?string $containerId;
    public ?string $defaultPassword;

    abstract public function __construct(
        ?string $containerName = null,
        ?string $containerId = null,
        ?string $defaultPassword = null,
    );
}