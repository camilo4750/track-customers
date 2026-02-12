<?php

namespace Internal\Clients\Infrastructure\Interfaces;

interface ClientRepositoryInterface
{
    public function findById(int $id): object|null;

    public function existById(int $id): bool;

    public function existEmail(string $email): bool;

    public function create(array $client): int;

    public function update(int $id, array $client): bool;

    public function findAll(?array $filters = []): array;

    public function delete(int $id): bool;
}

