<?php

namespace Internal\Users\Infrastructure\Http\Providers;

use Illuminate\Support\ServiceProvider;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Users\Infrastructure\Repositories\UserRepository;
use Internal\Users\Infrastructure\Interfaces\ModelHasRoleRepositoryInterface;
use Internal\Users\Infrastructure\Repositories\ModelHasRoleRepository;
use Internal\Users\Infrastructure\Interfaces\PermissionRepositoryInterface;
use Internal\Users\Infrastructure\Repositories\PermissionRepository;
use Internal\Users\Infrastructure\Interfaces\ModelHasPermissionRepositoryInterface;
use Internal\Users\Infrastructure\Repositories\ModelHasPermissionRepository;
class UserManagerProvider extends ServiceProvider
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
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            ModelHasRoleRepositoryInterface::class,
            ModelHasRoleRepository::class
        );
        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );
        $this->app->bind(
            ModelHasPermissionRepositoryInterface::class,
            ModelHasPermissionRepository::class
        );
    }
}
