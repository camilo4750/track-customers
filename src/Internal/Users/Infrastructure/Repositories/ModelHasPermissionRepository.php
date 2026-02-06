<?php

namespace Internal\Users\Infrastructure\Repositories;

use Internal\Users\Infrastructure\Interfaces\ModelHasPermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ModelHasPermissionRepository implements ModelHasPermissionRepositoryInterface
{
    public function getTableName(): string
    {
        return 'model_has_permissions';
    }

    public function getUser(int $id): ?User
    {
        $user = User::select('id', 'name', 'email', 'status')->find($id);
        
        if ($user === null) {
            return null;
        }

        return $user;
    }

    public function delete(User $user): void
    {
        DB::table($this->getTableName())
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->delete();
    }

    public function create(User $user, int $permissionId): self
    {
        DB::table($this->getTableName())
            ->insert([
                'model_id' => $user->id,
                'model_type' => get_class($user),
                'permission_id' => $permissionId,
            ]);

        return $this;
    }
}