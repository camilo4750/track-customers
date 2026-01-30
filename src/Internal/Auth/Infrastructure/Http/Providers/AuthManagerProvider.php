<?php

namespace Internal\Auth\Infrastructure\Http\Providers;

use Illuminate\Support\ServiceProvider;
use Internal\Auth\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Auth\Infrastructure\Repositories\UserRepository;
use Illuminate\Routing\Router;
use Internal\Auth\Infrastructure\Http\Middleware\JwtMiddleware;
class AuthManagerProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('jwt', JwtMiddleware::class);
    }
}