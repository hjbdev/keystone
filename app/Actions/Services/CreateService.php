<?php

namespace App\Actions\Services;

use App\Enums\ServiceCategory;
use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Jobs\Services\DeployService;
use App\Models\Server;

class CreateService
{
    public function execute(
        Server $server,
        string $name,
        ServiceCategory $category,
        ServiceType $type,
        string $version,
    ) {
        $driverName = "{$type->value}.{$version}";
        $service = $server->services()->create([
            'name' => $name,
            'category' => $category,
            'type' => $type, // postgres
            'version' => $version, // 17
            'driver_name' => $driverName, // postgres.17
            'status' => ServiceStatus::NOT_INSTALLED,
        ]);

        $service->credentials = $service->driver()->defaultCredentials();
        $service->save();

        dispatch(new DeployService($service));

        return $service;
    }
}
