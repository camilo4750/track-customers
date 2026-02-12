<?php

namespace Internal\Clients\Infrastructure\Http\Providers;

use Illuminate\Support\ServiceProvider;
use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Internal\Clients\Infrastructure\Repositories\ClientRepository;

class ClientManagerProvider extends ServiceProvider
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
            ClientRepositoryInterface::class,
            ClientRepository::class
        );
    }
}

