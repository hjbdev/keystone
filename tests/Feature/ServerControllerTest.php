<?php

use App\Actions\GetProviderService;
use App\Data\ServerProviders\CreatedServer;
use App\Models\Organisation;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // If you have database migrations or any setup, include it here
    // For example, using Laravel's RefreshDatabase trait
    // use Illuminate\Foundation\Testing\RefreshDatabase;

    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('index route displays servers for an organisation', function () {
    $organisation = Organisation::factory()->create();
    Server::factory()->count(2)->create(['organisation_id' => $organisation->id]);

    $response = $this->get(route('servers.index', ['organisation' => $organisation->id]));
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('servers/Index'));
});

test('create route returns inertia view', function () {
    $organisation = Organisation::factory()->create();
    $response = $this->get(route('servers.create', ['organisation' => $organisation->id]));
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
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

    $response->assertSessionHas('error', 'Invalid provider');
    $response->assertStatus(302); // redirect back
});

test('store route creates a server with valid data', function () {
    $organisation = Organisation::factory()->create();

    $response = $this->post(route('servers.store', ['organisation' => $organisation->id]), [
        'provider' => 'hetzner',
        'server_type' => 'cx11',
        'location' => 'hel1',
        'image' => 'ubuntu-20.04',
    ]);

    $response->assertRedirectContains('/servers/');
    $this->assertDatabaseHas('servers', [
        'organisation_id' => $organisation->id,
        'provider' => 'hetzner',
        'region' => 'hel1',
        'os' => 'ubuntu-20.04',
    ]);
});

test('show route displays a single server', function () {
    $organisation = Organisation::factory()->create();
    $server = Server::factory()->create(['organisation_id' => $organisation->id]);

    $response = $this->get(route('servers.show', [
        'organisation' => $organisation->id,
        'server' => $server->id,
    ]));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('servers/Show'));
});
