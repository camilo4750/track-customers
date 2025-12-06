<?php

use Illuminate\Support\Facades\Route;
use Internal\Users\Http\Controllers\UserController;

Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

