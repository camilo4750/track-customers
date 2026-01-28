<?php

use Illuminate\Support\Facades\Route;
use Internal\Users\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::prefix('api')->group(function () {
    Route::get('/users', [UserController::class, 'indexApi'])->name('users.indexApi');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

