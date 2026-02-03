<?php

namespace Internal\Users\Infrastructure\Interfaces;

use App\Models\User;

interface ModelHasRoleRepositoryInterface
{
    public function getTableName(): string;

    public function delete(User $user): void;
    public function create(User $user, string $role): self;
}