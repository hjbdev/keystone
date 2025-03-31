<?php

namespace App\Drivers;

abstract class DatabaseDriver implements Driver
{
    public string $defaultUser = 'keystone';
    public string $defaultDb = 'keystone';

    abstract public function __construct(
        public ?string $containerName = null,
        public ?string $containerId = null,
        public ?string $defaultPassword = null,
    );
}