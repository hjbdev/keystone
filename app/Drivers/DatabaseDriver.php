<?php

namespace App\Drivers;

interface DatabaseDriver extends Driver
{
    public string $defaultUser = 'keystone';
    public string $defaultDb = 'keystone';

    public function __construct(
        public ?string $containerName = null,
        public ?string $containerId = null,
        public ?string $defaultPassword = null,
    );
}