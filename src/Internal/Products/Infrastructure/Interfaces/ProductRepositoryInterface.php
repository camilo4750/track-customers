<?php

namespace Internal\Products\Infrastructure\Interfaces;

interface ProductRepositoryInterface
{
    public function findById(int $id): object|null;

    public function existById(int $id): bool;

    public function existSku(string $sku, ?int $excludeId = null): bool;

    public function create(array $product): int;

    public function update(int $id, array $product): bool;

    public function findAll(?array $filters = []): array;

    public function delete(int $id): bool;
}
