<?php

namespace Internal\Users\Infrastructure\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?array;

    public function findById(int $id): ?User;

    public function create(array $user): int;

    public function update(int $id, array $user): bool;

    public function findAll(?array $filters = []): array;

    public function delete(int $id): bool;
}

