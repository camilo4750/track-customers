<?php

namespace Internal\Seeders\Application\List;

class ListSeedersHandler
{
    public function handle(): array
    {
        $seeders = config('seeders.seeders', []);

        return array_values(array_map(static function (array $item) {
            return [
                'key' => $item['key'],
                'module' => $item['module'],
                'description' => $item['description'],
                'defaultCount' => $item['defaultCount'],
            ];
        }, $seeders));
    }
}
