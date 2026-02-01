<?php

return [
    App\Providers\AppServiceProvider::class,
    Internal\Auth\Infrastructure\Http\Providers\AuthManagerProvider::class,
    Internal\Users\Infrastructure\Http\Providers\UserManagerProvider::class,
];
