<?php

namespace Internal\Products\Infrastructure\Seeders;

use Internal\Seeders\Application\Contracts\RunnableSeederInterface;
use Illuminate\Support\Facades\DB;

class ProductSeeder implements RunnableSeederInterface
{
    private const BATCH_SIZE = 100;

    public function runWithCount(int $count): int
    {
        $faker = fake();
        $created = 0;
        $batch = [];
        $categories = ['Electrónica', 'Ropa', 'Hogar', 'Deportes', 'Juguetes', 'Herramientas', 'Alimentación', 'Bebidas'];

        for ($i = 0; $i < $count; $i++) {
            $batch[] = [
                'name' => $faker->words(3, true),
                'sku' => strtoupper($faker->unique()->regexify('[A-Z0-9]{8,12}')),
                'price' => $faker->randomFloat(2, 1, 9999.99),
                'category' => $faker->randomElement($categories),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $created++;

            if (count($batch) >= self::BATCH_SIZE) {
                DB::table('products')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('products')->insert($batch);
        }

        return $created;
    }
}
