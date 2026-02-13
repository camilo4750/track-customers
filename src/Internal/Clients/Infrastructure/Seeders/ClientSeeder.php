<?php

namespace Internal\Clients\Infrastructure\Seeders;

use Internal\Seeders\Application\Contracts\RunnableSeederInterface;
use Illuminate\Support\Facades\DB;

class ClientSeeder implements RunnableSeederInterface
{
    private const BATCH_SIZE = 100;

    public function runWithCount(int $count): int
    {
        $faker = fake();
        $created = 0;
        $batch = [];

        for ($i = 0; $i < $count; $i++) {
            $batch[] = [
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'status' => $faker->randomElement(['active', 'inactive']),
                'tags' => json_encode($faker->optional(0.5)->randomElements(['vip', 'empresa', 'particular', 'recurrente'], $faker->numberBetween(0, 3)) ?: []),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $created++;

            if (count($batch) >= self::BATCH_SIZE) {
                DB::table('clients')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('clients')->insert($batch);
        }

        return $created;
    }
}
