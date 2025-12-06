<?php

namespace Internal\Users\Http\Providers;

use Illuminate\Support\ServiceProvider;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Users\Infrastructure\Repositories\UserRepository;

class UserManagerProvider extends ServiceProvider
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
        //
    }
}

