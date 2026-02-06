<?php

namespace Internal\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Internal\Users\Infrastructure\Interfaces\PermissionRepositoryInterface;
use App\Models\User;
class PermissionRepository implements PermissionRepositoryInterface
{
    public function getTableName(): string
    {
        return 'permissions';
    }

    public function getUser(int $id): ?User
    {
        $user = User::select('id', 'name', 'email', 'status')->find($id);
        
        if ($user === null) {
            return null;
        }

        return $user;
    }

    public function getPermissions(): Collection
    {
        return DB::table($this->getTableName())
            ->select('id', 'name')
            ->where('guard_name', 'api')
            ->orderBy('name')
            ->get();
    }
}