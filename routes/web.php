<?php

use App\Enums\ServerStatus;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ServerController;
use App\Models\Server;
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

    $keystonePublicKey = file_get_contents(storage_path('app/private/ssh/id_ed25519.pub'));

    $script = str_replace('[!hostname!]', $validated['hostname'], $script);
    $script = str_replace('[!sudo_password!]', $validated['sudo_password'], $script);
    $script = str_replace('[!server_id!]', $validated['server_id'], $script);
    $script = str_replace('[!keystonepublickey!]', $keystonePublicKey, $script);
    $script = str_replace('[!callback!]', route('provision.callback'), $script);

    return response($script)
        ->header('Content-Type', 'text/plain');
})->name('provision-script');

Route::post('/provision-callback', function (Request $request) {
    $validated = $request->validate([
        'server_id' => ['required', 'integer', 'exists:servers,id'],
    ]);

    $server = Server::find($validated['server_id']);

    if (! in_array($request->ip(), [$server->ipv4, $server->ipv6])) {
        logger('someone tried to callback from an invalid IP');
        logger(' server ip: ' . $server->ipv4);
        logger(' server ipv6: ' . $server->ipv6);
        logger(' callback ip: ' . $request->ip());
        logger(' server id: ' . $server->id);
        return response('Unauthorized', 401);
    }

    $server->update([
        'status' => ServerStatus::ACTIVE,
    ]);

    return response('OK', 200);
})->name('provision.callback');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
