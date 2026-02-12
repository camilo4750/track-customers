<?php

use Illuminate\Support\Facades\Route;
use Internal\Clients\Infrastructure\Http\Controllers\ClientController;

Route::get('/clients', [ClientController::class, 'index'])
    ->name('clients.index');

Route::prefix('api')
    ->middleware(['api', 'jwt'])
    ->group(function () {
        Route::get('/clients', [ClientController::class, 'indexApi'])
            ->name('clients.indexApi');

        Route::post('/clients', [ClientController::class, 'store'])
            ->name('clients.store');

        Route::put('/clients/{id}', [ClientController::class, 'update'])
            ->name('clients.update');

        Route::delete('/clients/{id}', [ClientController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('clients.destroy');
    });

