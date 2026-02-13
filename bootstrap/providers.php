<?php

return [
    App\Providers\AppServiceProvider::class,
    Internal\Auth\Infrastructure\Http\Providers\AuthManagerProvider::class,
    Internal\Users\Infrastructure\Http\Providers\UserManagerProvider::class,
    Internal\Clients\Infrastructure\Http\Providers\ClientManagerProvider::class,
    Internal\Products\Infrastructure\Http\Providers\ProductManagerProvider::class,
    Internal\Seeders\Infrastructure\Http\Providers\SeederManagerProvider::class,
];
