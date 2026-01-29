<?php

namespace Internal\Users\Infrastructure\Interfaces;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?array;

    public function findById(int $id): ?array;

    public function create(array $user): int;

    public function update(int $id, array $user): bool;

    public function findAll(?array $filters = []): array;

    public function delete(int $id): bool;
}

