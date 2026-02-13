<?php

use Illuminate\Support\Facades\Route;
use Internal\Seeders\Infrastructure\Http\Controllers\SeederController;

Route::get('/seeders', [SeederController::class, 'index'])
    ->middleware('role:admin')
    ->name('seeders.index');

Route::prefix('api')
    ->middleware(['api', 'jwt', 'role:admin'])
    ->group(function () {
        Route::get('/seeders', [SeederController::class, 'list'])
            ->name('seeders.list');

        Route::post('/seeders/run', [SeederController::class, 'run'])
            ->name('seeders.run');
    });
