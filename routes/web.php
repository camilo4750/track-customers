<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// Rutas de autenticación (vistas)
Route::get('/', function () {
    return Inertia::render('auth/pages/Login');
})->name('login.view');

Route::get('/dashboard', function () {
    return Inertia::render('dashboard/pages/Dashboard');
})->name('dashboard');