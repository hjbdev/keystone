<?php

namespace App\Console\Commands;

use App\Actions\Services\CreateService;
use App\Enums\ServiceCategory;
use App\Enums\ServiceType;
use App\Models\Server;
use Illuminate\Console\Command;

class CreateServiceCommand extends Command
{
    protected $signature = 'service:create';
    protected $description = 'Create a service';

    public function handle()
    {
        $serverId = $this->components->ask('Enter the server ID');
        $server = Server::find($serverId);

        if (!$server) {
            $this->components->error('Server not found');
            return;
        }

        $serviceType = $this->components->choice('select the service you want to install', [
            'postgres-17'
        ]);

        $serviceName = $this->components->ask('Enter the service name');

        $service = app(CreateService::class)->execute(
            server: $server,
            name: $serviceName,
            category: ServiceCategory::DATABASE,
            type: ServiceType::POSTGRES,
            version: '17',
            driverName: $serviceType,
        );

        $this->components->info('Service created successfully');
        dump($service);
    }
}
