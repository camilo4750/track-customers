<?php

namespace Internal\Users\Infrastructure\Interfaces;

use App\Models\User;

interface ModelHasPermissionRepositoryInterface
{
    public function getUser(int $id): ?User;

    public function delete(User $user): void;

    public function create(User $user, int $permissionId): self;
}