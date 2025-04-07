<?php

namespace Database\Seeders;

use App\Enums\NetworkType;
use App\Enums\OrganisationRole;
use App\Enums\ProviderType;
use App\Enums\RepositoryType;
use App\Models\Organisation;
use App\Models\Server;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Harry',
            'email' => 'harry@hjb.dev',
            'password' => env('DEFAULT_PASSWORD') ?: Hash::make('password'),
        ]);

        $organisation = Organisation::create([
            'name' => 'Stratbucket',
            'slug' => 'stratbucket',
            'owner_id' => 1,
        ]);

        $provider = $organisation->providers()->create([
            'name' => 'Hetzner',
            'type' => ProviderType::HETZNER,
            'token' => env('HETZNER_KEY'),
        ]);

        $network = $organisation->networks()->create([
            'type' => NetworkType::EXTERNAL,
            'name' => 'keystone',
            'external_id' => 'net-12345',
            'provider_id' => $provider->id,
            'ip_range' => fake()->ipv4() . '/24',
        ]);

        $servers = Server::factory(40)
            ->forNetwork($network->id)
            ->forOrganisation($organisation->id)
            ->forProvider($provider->id)
            ->create();

        $organisation->servers()->saveMany($servers);

        $organisation->members()->attach($user, ['role' => OrganisationRole::ADMIN]);

        $application = $organisation->applications()->create([
            'name' => 'ClipBin',
            'repository_url' => 'git@github.com:hjbdev/clipbin.git',
            'repository_type' => RepositoryType::GIT,
        ]);

        $application->environments()->create([
            'name' => 'Dev',
            'branch' => 'main',
            'url' => 'https://dev.clipbin.hjb.dev',
            'status' => 'active',
        ]);
    }
}
