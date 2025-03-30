<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ServerController;
use Illuminate\Http\Request;
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

Route::get('/provision-script', function (Request $request) {
    $validated = $request->validate([
        'sudo_password' => ['required', 'string'],
        'hostname' => ['required', 'string'],
        'server_id' => ['required', 'integer', 'exists:servers,id'],
    ]);

    $script = file_get_contents(base_path('provision.sh'));

    $script = str_replace('[!hostname!]', $validated['hostname'], $script);
    $script = str_replace('[!sudo_password!]', $validated['sudo_password'], $script);
    $script = str_replace('[!server_id!]', $validated['server_id'], $script);

    return response($script)
        ->header('Content-Type', 'text/plain');
})->name('provision-script');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
