<?php

use Illuminate\Support\Facades\Route;
use Internal\Auth\Infrastructure\Http\Controllers\AuthController;
use Inertia\Inertia;

Route::prefix('api')->middleware('api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    });
    Route::prefix('auth')->middleware('jwt')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});