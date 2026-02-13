<?php

use Illuminate\Support\Facades\Route;
use Internal\Products\Infrastructure\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::prefix('api')
    ->middleware(['api', 'jwt'])
    ->group(function () {
        Route::get('/products', [ProductController::class, 'indexApi'])
            ->name('products.indexApi');

        Route::post('/products', [ProductController::class, 'store'])
            ->name('products.store');

        Route::put('/products/{id}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::delete('/products/{id}', [ProductController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('products.destroy');
    });
