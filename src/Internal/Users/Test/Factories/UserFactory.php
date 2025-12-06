<?php

namespace Internal\Users\Test\Factories;

class UserFactory
{
    public static function make(array $attributes = []): array
    {
        $defaults = [
            'id' => null,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return array_merge($defaults, $attributes);
    }

    public static function create(array $attributes = []): array
    {
        return self::make($attributes);
    }
}

