<?php

namespace Database\Seeders;

use App\Enums\OrganisationRole;
use App\Enums\ProviderType;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestEnvironmentSeeder
{
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

        $organisation->members()->attach($user, ['role' => OrganisationRole::ADMIN]);

        $organisation->providers()->create([
            'name' => 'Hetzner',
            'type' => ProviderType::HETZNER,
            'token' => env('HETZNER_KEY'),
        ]);
    }
}
