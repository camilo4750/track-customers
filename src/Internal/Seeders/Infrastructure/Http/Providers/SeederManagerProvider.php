<?php

namespace Internal\Seeders\Infrastructure\Http\Providers;

use Illuminate\Support\ServiceProvider;

class SeederManagerProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}
