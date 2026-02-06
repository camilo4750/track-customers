<?php

use Illuminate\Support\Facades\Route;
use Internal\Users\Infrastructure\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index'])
    ->name('users.index');

Route::get('/users/{id}/permissions', [UserController::class, 'permissions'])
    ->name('users.permissions');

Route::prefix('api')
    ->middleware(['api', 'jwt', 'role:admin'])
    ->group(function () {

        Route::get('/users', [UserController::class, 'indexApi'])
            ->name('users.indexApi');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::put('/users/{id}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/users/{id}', [UserController::class, 'destroy'])
            ->name('users.destroy');

        Route::get('/users/{id}/permissions', [UserController::class, 'permissionsApi'])
            ->name('users.permissions.api');

        Route::put('/users/{id}/permissions-sync', [UserController::class, 'syncPermissions'])
            ->name('users.permissions.sync');
    });