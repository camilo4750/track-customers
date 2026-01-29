<?php

namespace Internal\Users\Infrastructure\Http\Providers;

use Illuminate\Support\ServiceProvider;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Users\Infrastructure\Repositories\UserRepository;

class UserManagerProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRoutes();
        $this->registerBindings();
    }

    public function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }

    public function registerBindings(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    public function registerMiddleware(): void
    {
        $router = $this->app['router'];
    }

    public function boot(): void
    {
        //
    }
}
