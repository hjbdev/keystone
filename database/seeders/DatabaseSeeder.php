<?php

namespace Database\Seeders;

use App\Enums\OrganisationRole;
use App\Enums\RepositoryType;
use App\Models\Organisation;
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
        // User::factory(10)->create();

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

        $organisation->members()->attach($user, ['role'  => OrganisationRole::Admin]);

        $application = $organisation->applications()->create([
            'name' => 'ClipBin',
            'repository_url' => 'git@github.com:hjbdev/clipbin.git',
            'repository_type' => RepositoryType::Git,
        ]);

        $application->environments()->create([
            'name' => 'Dev',
            'branch' => 'main',
            'url' => 'https://dev.clipbin.hjb.dev',
            'status' => 'active'
        ]);
    }
}
