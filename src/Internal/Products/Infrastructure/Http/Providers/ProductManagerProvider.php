<?php

namespace Internal\Products\Infrastructure\Http\Providers;

use Illuminate\Support\ServiceProvider;
use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Internal\Products\Infrastructure\Repositories\ProductRepository;

class ProductManagerProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerBindings();
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }

    public function registerBindings(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
    }
}
