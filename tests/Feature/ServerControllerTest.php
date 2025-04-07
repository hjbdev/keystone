<?php

use App\Data\ServerProviders\CreatedServer;
use App\Enums\NetworkType;
use App\Enums\ProviderType;
use App\Models\Organisation;
use App\Models\Provider;
use App\Models\Server;
use App\Models\User;
use App\Services\ServerProviders\HetznerService;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Mockery\MockInterface;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // If you have database migrations or any setup, include it here
    // For example, using Laravel's RefreshDatabase trait
    // use Illuminate\Foundation\Testing\RefreshDatabase;

    /** @var User $user */
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('index route displays servers for an organisation', function () {
    $organisation = Organisation::factory()->create();
    $provider = Provider::factory()->forOrganisation($organisation->id)->create();
    $network = $organisation->networks()->create([
        'type' => NetworkType::EXTERNAL,
        'name' => 'keystone',
        'external_id' => 'net-12345',
        'provider_id' => $provider->id,
        'ip_range' => fake()->ipv4() . '/24',
    ]);

    Server::factory()->count(2)->create([
        'provider_id' => $provider->id,
        'organisation_id' => $organisation->id,
        'external_network_id' => $network->id,
    ]);

    $response = $this->get(route('servers.index', ['organisation' => $organisation->id]));
    $response->assertStatus(200);
    $response->assertInertia(fn(AssertableInertia $page) => $page
        ->component('servers/Index'));
});

test('create route returns inertia view', function () {
    $organisation = Organisation::factory()->create();
    $response = $this->get(route('servers.create', ['organisation' => $organisation->id]));
    $response->assertStatus(200);
    $response->assertInertia(fn(AssertableInertia $page) => $page
        ->component('servers/Create'));
});

test('store route fails with invalid provider', function () {
    $organisation = Organisation::factory()->create();

    $response = $this->post(route('servers.store', ['organisation' => $organisation->id]), [
        'provider' => 'invalid_provider',
        'server_type' => 'cx11',
        'location' => 'hel1',
        'image' => 'ubuntu-20.04',
    ]);

    $response->assertSessionHasErrors(['provider' => 'The selected provider is invalid.']);
    $response->assertStatus(302); // redirect back
});

test('store route creates a server with valid data', function () {
    $organisation = Organisation::factory()->create();

    // Create a real provider first, then partially mock it
    $provider = Provider::factory()->create([
        'name' => 'hetzner',
        'type' => ProviderType::HETZNER,
        'token' => Str::uuid(),
        'organisation_id' => $organisation->id
    ]);

    $network = $organisation->networks()->create([
        'type' => NetworkType::EXTERNAL,
        'name' => 'keystone',
        'external_id' => 'net-12345',
        'provider_id' => $provider->id,
        'ip_range' => fake()->ipv4() . '/24',
    ]);

    $this->mock(HetznerService::class, function (MockInterface $mock) use ($network) {
        $mock->shouldReceive('createServer')
            ->once()
            ->with(
                Mockery::on(function ($arg) {
                    return is_string($arg['name']) && strlen($arg['name']) > 0;
                }),
                'cx11',
                'hel1',
                'ubuntu-20.04',
                $network->external_id
            )
            ->andReturn(new CreatedServer(
                name: 'test-server-from-mock',
                rootPassword: 'password123',
                id: 'srv-12345',
                status: 'running',
                ipv4: '192.0.2.100',
                ipv6: '2001:db8::100',
                networkId: $network->external_id,
            ));

        $mock->shouldReceive('createNetwork')->never();
    });

    $response = $this->post(route('servers.store', ['organisation' => $organisation->id]), [
        'provider' => $provider->id,
        'server_type' => 'cx11',
        'location' => 'hel1',
        'image' => 'ubuntu-20.04',
    ]);

    $response->assertRedirectContains('/servers/');
    $this->assertDatabaseHas('servers', [
        'organisation_id' => $organisation->id,
        'provider_id' => $provider->id,
        'region' => 'hel1',
        'os' => 'ubuntu-20.04',
        'external_network_id' => $network->id,
    ]);
});

test('show route displays a single server', function () {
    $organisation = Organisation::factory()->create();
    $network = $organisation->networks()->create([
        'type' => NetworkType::EXTERNAL,
        'name' => 'keystone',
        'external_id' => 'net-12345',
    ]);
    $server = Server::factory()->create([
        'organisation_id' => $organisation->id,
        'external_network_id' => $network->id,
    ]);

    $response = $this->get(route('servers.show', [
        'organisation' => $organisation->id,
        'server' => $server->id,
    ]));

    $response->assertStatus(200);
    $response->assertInertia(fn(AssertableInertia $page) => $page
        ->component('servers/Show'));
});
