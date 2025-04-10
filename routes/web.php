<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ProvisionCallback;
use App\Http\Controllers\ProvisionScript;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return inertia('Dashboard', [
            'organisations' => auth()->user()->organisations,
        ]);
    })->name('dashboard');

    Route::prefix('organisations/{organisation}')->group(function () {
        Route::get('/', [OrganisationController::class, 'show'])->name('organisations.show');

        Route::resource('servers', ServerController::class)
            ->only('index', 'show', 'create', 'store')
            ->name('index', 'servers.index')
            ->name('show', 'servers.show')
            ->name('create', 'servers.create')
            ->name('store', 'servers.store');

        Route::prefix('servers/{server}')->group(function () {
            Route::resource('services', ServiceController::class)
                ->only('create', 'store')
                ->name('create', 'services.create')
                ->name('store', 'services.store');
        });

        Route::resource('applications', ApplicationController::class)
            ->only('show', 'index')
            ->name('index', 'applications.index')
            ->name('show', 'applications.show');

        Route::prefix('applications/{application}')->group(function () {
            Route::resource('environments', EnvironmentController::class)
                ->only('show')
                ->name('show', 'environments.show');
        });
    });
});

Route::get('/provision-script', ProvisionScript::class)->name('provision-script');
Route::post('/provision-callback', ProvisionCallback::class)->name('provision.callback');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
