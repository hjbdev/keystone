<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Deployment;
use App\Models\Environment;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\Server;
use App\Models\Service;
use App\Models\Slice;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'application' => Application::class,
            'deployment' => Deployment::class,
            'environment' => Environment::class,
            'organisation' => Organisation::class,
            'organisation-user' => OrganisationUser::class,
            'server' => Server::class,
            'service' => Service::class,
            'slice' => Slice::class,
            'step' => Step::class,
            'user' => User::class,
        ]);
    }
}
