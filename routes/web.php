<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\OrganisationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('organisations/{organisation}')->group(function () {
        Route::get('/', [OrganisationController::class, 'show'])->name('organisations.show');
        Route::resource('applications', ApplicationController::class)
            ->only('show')
            ->name('show', 'applications.show');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
