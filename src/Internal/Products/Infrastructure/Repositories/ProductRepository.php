<?php

namespace Internal\Products\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function getTableName(): string
    {
        return 'products';
    }

    public function findById(int $id): object|null
    {
        $product = DB::table($this->getTableName())
            ->select('id', 'name', 'sku', 'price', 'category', 'created_at', 'updated_at')
            ->where('id', $id)
            ->first();

        if (!$product) {
            return null;
        }

        return $product;
    }

    public function existById(int $id): bool
    {
        return DB::table($this->getTableName())
            ->where('id', $id)
            ->exists();
    }

    public function existSku(string $sku, ?int $excludeId = null): bool
    {
        $query = DB::table($this->getTableName())
            ->where('sku', $sku);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function create(array $product): int
    {
        return DB::table($this->getTableName())
            ->insertGetId(
                [
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'price' => $product['price'],
                    'category' => $product['category'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }

    public function update(int $id, array $product): bool
    {
        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->update(
                [
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'price' => $product['price'],
                    'category' => $product['category'] ?? null,
                    'updated_at' => now(),
                ]
            );

        return $affected > 0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAll(?array $filters = []): array
    {
        $query = DB::table($this->getTableName())
            ->select('id', 'name', 'sku', 'price', 'category', 'created_at', 'updated_at');

        if (!empty($filters['category'])) {
            $query->where('category', '=', $filters['category']);
        }

        return $query->orderBy('id', 'desc')->get()->toArray();
    }

    public function delete(int $id): bool
    {
        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->delete();

        return $affected > 0;
    }
}
