<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('organisations/{organisation}')->group(function () {
        Route::get('/', [OrganisationController::class, 'show'])->name('organisations.show');

        Route::resource('servers', ServerController::class)
            ->only('index', 'show', 'create', 'store')
            ->name('index', 'servers.index')
            ->name('show', 'servers.show')
            ->name('create', 'servers.create')
            ->name('store', 'servers.store');
        
        Route::resource('applications', ApplicationController::class)
            ->only('show')
            ->name('show', 'applications.show');

        Route::prefix('applications/{application}')->group(function () {
            Route::resource('environments', EnvironmentController::class)
                ->only('show')
                ->name('show', 'environments.show');
        });
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
