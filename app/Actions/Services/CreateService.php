<?php

namespace App\Actions\Services;

use App\Enums\ServiceCategory;
use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Jobs\Services\InstallService;
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
        string $driverName,
    ) {
        $service = $server->services()->create([
            'name' => $name,
            'category' => $category,
            'type' => $type,
            'version' => $version, // 17
            'driver_name' => $driverName, // postgres
            'status' => ServiceStatus::NOT_INSTALLED,
        ]);
    }
}
