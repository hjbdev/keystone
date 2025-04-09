<?php

use App\Actions\Services\CreateService;
use App\Drivers\Driver;
use App\Drivers\Postgres\Postgres17Driver;
use App\Enums\NetworkType;
use App\Enums\ServiceCategory;
use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Jobs\Services\DeployService;
use App\Models\Network;
use App\Models\Provider;
use App\Models\Server;
use App\Models\Service;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

function setupTestEnvironment() {
    $user = User::factory()->create();
    
    $organisation = Organisation::factory()->create([
        'owner_id' => $user->id
    ]);
    
    $provider = Provider::factory()->create([
        'organisation_id' => $organisation->id
    ]);
    
    $network = Network::create([
        'name' => 'test-network',
        'ip_range' => '10.0.0.0/24',
        'type' => NetworkType::EXTERNAL,
        'external_id' => 'ext-12345',
        'organisation_id' => $organisation->id,
        'provider_id' => $provider->id,
    ]);

    $server = Server::factory()->create([
        'organisation_id' => $organisation->id,
        'provider_id' => $provider->id,
        'external_network_id' => $network->id,
    ]);

    return [
        'user' => $user,
        'organisation' => $organisation,
        'provider' => $provider,
        'network' => $network,
        'server' => $server,
    ];
}

test('create service page is accessible', function () {
    $setup = setupTestEnvironment();
    
    $this->actingAs($setup['user']);
    
    $response = $this->get(route('services.create', [
        'organisation' => $setup['organisation']->id,
        'server' => $setup['server']->id
    ]));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('services/Create')
        ->has('server')
        ->has('services')
    );
});

test('store service with valid data', function () {
    $setup = setupTestEnvironment();
    
    $this->actingAs($setup['user']);
    
    $mockDefaultCredentials = [
        'user' => 'test-user',
        'password' => 'test-password',
        'db' => 'test-db'
    ];
    
    $mockDriver = Mockery::mock(Driver::class);
    $mockDriver->shouldReceive('defaultCredentials')->andReturn($mockDefaultCredentials);
    
    // intercept the driver
    $this->partialMock(Service::class, function ($mock) use ($mockDriver) {
        $mock->shouldReceive('driver')->andReturn($mockDriver);
    });
    
    Bus::fake();

    $data = [
        'name' => 'test-postgres-database',
        'category' => ServiceCategory::DATABASE->value,
        'type' => ServiceType::POSTGRES->value,
        'version' => '17',
    ];

    $response = $this->post(route('services.store', [
        'organisation' => $setup['organisation']->id,
        'server' => $setup['server']->id
    ]), $data);

    // Since we're not mocking the entire CreateService action, we should get a proper redirect
    $response->assertRedirect(route('servers.show', [
        'organisation' => $setup['organisation']->id,
        'server' => $setup['server']->id
    ]));
    $response->assertSessionHas('success', 'Service created successfully');
    
    $this->assertDatabaseHas('services', [
        'name' => 'test-postgres-database',
        'server_id' => $setup['server']->id,
        'category' => ServiceCategory::DATABASE->value,
        'type' => ServiceType::POSTGRES->value,
        'version' => '17',
        'driver_name' => 'postgres.17',
        'status' => ServiceStatus::NOT_INSTALLED->value,
    ]);
    
    Bus::assertDispatched(DeployService::class);
});

test('store service with invalid data', function () {
    $setup = setupTestEnvironment();
    
    $this->actingAs($setup['user']);

    $data = [
        'name' => '', // Invalid name
        'category' => 'invalid-category',
        'type' => 'invalid-type',
        'version' => 'invalid-version',
    ];

    $response = $this->post(route('services.store', [
        'organisation' => $setup['organisation']->id,
        'server' => $setup['server']->id
    ]), $data);

    $response->assertSessionHasErrors(['name', 'category', 'type', 'version']);
});

test('store service validates version exists in config', function () {
    $setup = setupTestEnvironment();
    
    $this->actingAs($setup['user']);
    
    // Mock the config to simulate the version not existing
    Config::set('keystone.services.' . ServiceCategory::DATABASE->value . '.' . ServiceType::POSTGRES->value . '.versions', [
        '16' => [
            'name' => 'PostgreSQL 16',
            'description' => 'PostgreSQL 16',
            'image' => 'postgres:16',
        ]
    ]);
    
    $data = [
        'name' => 'test-postgres-database',
        'category' => ServiceCategory::DATABASE->value,
        'type' => ServiceType::POSTGRES->value,
        'version' => '17', // This version doesn't exist in our mocked config
    ];

    $response = $this->post(route('services.store', [
        'organisation' => $setup['organisation']->id,
        'server' => $setup['server']->id
    ]), $data);
    
    $response->assertSessionHasErrors(['version']);
});

test('store service with non-existent server returns 404', function () {
    $setup = setupTestEnvironment();
    
    $this->actingAs($setup['user']);
    
    $data = [
        'name' => 'test-postgres-database',
        'category' => ServiceCategory::DATABASE->value,
        'type' => ServiceType::POSTGRES->value,
        'version' => '17',
    ];

    $response = $this->post(route('services.store', [
        'organisation' => $setup['organisation']->id,
        'server' => 9999
    ]), $data);
    
    $response->assertStatus(404);
});

test('create service page with non-existent server returns 404', function () {
    $setup = setupTestEnvironment();
    
    $this->actingAs($setup['user']);
    
    $response = $this->get(route('services.create', [
        'organisation' => $setup['organisation']->id,
        'server' => 9999
    ]));
    
    $response->assertStatus(404);
});

test('store service is properly created and dispatched', function () {
    $setup = setupTestEnvironment();
    $this->actingAs($setup['user']);
    
    // Setup mock credentials and driver
    $mockDriver = Mockery::mock(Driver::class)->shouldReceive('defaultCredentials')
        ->andReturn([
            'user' => 'test-user',
            'password' => 'test-password',
            'db' => 'test-db'
        ])
        ->getMock();
    
    // Setup test data
    $testData = [
        'name' => 'test-postgres-database',
        'category' => ServiceCategory::DATABASE->value,
        'type' => ServiceType::POSTGRES->value,
        'version' => '17',
    ];
    
    // Mock service class to return our mock driver
    $this->partialMock(Service::class, function ($mock) use ($mockDriver) {
        $mock->shouldReceive('driver')->andReturn($mockDriver);
    });
    
    // Mock CreateService action
    $this->mock(CreateService::class, function ($mock) use ($setup, $testData) {
        $service = new Service([
            'id' => 1,
            'server_id' => $setup['server']->id,
            'name' => $testData['name'],
            'category' => ServiceCategory::DATABASE,
            'type' => ServiceType::POSTGRES,
            'version' => $testData['version'],
            'driver_name' => 'postgres.17',
            'status' => ServiceStatus::NOT_INSTALLED,
        ]);
        
        $service->setRelation('server', $setup['server']);
        
        $mock->shouldReceive('execute')
            ->once()
            ->withArgs(function ($server, $name, $category, $type, $version) use ($setup, $testData) {
                return $server->id === $setup['server']->id
                    && $name === $testData['name']
                    && $category === ServiceCategory::DATABASE
                    && $type === ServiceType::POSTGRES
                    && $version === $testData['version'];
            })
            ->andReturn($service);
    });
    
    Bus::fake();

    // Execute request
    $response = $this->post(route('services.store', [
        'organisation' => $setup['organisation']->id,
        'server' => $setup['server']->id
    ]), $testData);

    // Assert response
    $response->assertRedirect(route('servers.show', [
        'organisation' => $setup['organisation']->id,
        'server' => $setup['server']->id
    ]));
    $response->assertSessionHas('success', 'Service created successfully');

    // Assert database state
    $this->assertDatabaseHas('services', [
        'name' => $testData['name'],
        'server_id' => $setup['server']->id,
        'category' => ServiceCategory::DATABASE->value,
        'type' => ServiceType::POSTGRES->value,
        'version' => $testData['version'],
        'driver_name' => 'postgres.17',
        'status' => ServiceStatus::NOT_INSTALLED->value,
    ]);
    
    // Assert job was dispatched
    Bus::assertDispatched(DeployService::class);
});