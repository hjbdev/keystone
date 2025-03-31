<?php

namespace App\Actions\Services;

use App\Enums\ServiceCategory;
use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Jobs\Services\DeployService;
use App\Models\Server;
use Illuminate\Support\Str;

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

        $defaultPassword = Str::random(16);

        dispatch(new DeployService($service, $defaultPassword));

        return ['defaultPassword' => $defaultPassword, 'service' => $service];
    }
}
