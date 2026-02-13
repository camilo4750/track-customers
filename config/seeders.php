<?php

return [
    'seeders' => [
        'clients' => [
            'key' => 'clients',
            'module' => 'Clientes',
            'description' => 'Genera clientes de prueba con nombre, email, teléfono, estado y tags.',
            'defaultCount' => 100,
            'seederClass' => \Internal\Clients\Infrastructure\Seeders\ClientSeeder::class,
        ],
        'products' => [
            'key' => 'products',
            'module' => 'Productos',
            'description' => 'Genera productos de prueba con nombre, SKU único, precio y categoría.',
            'defaultCount' => 300,
            'seederClass' => \Internal\Products\Infrastructure\Seeders\ProductSeeder::class,
        ],
    ],
];
