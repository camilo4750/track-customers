<?php

namespace Internal\Users\Infrastructure\Interfaces;

use Illuminate\Support\Collection;
use App\Models\User;
interface PermissionRepositoryInterface
{
    public function getUser(int $id): ?User;
    public function getPermissions(): Collection;
}