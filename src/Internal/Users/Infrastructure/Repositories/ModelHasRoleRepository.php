<?php

namespace Internal\Users\Infrastructure\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\ModelHasRole;
use Spatie\Permission\Models\Role;
use Internal\Users\Infrastructure\Interfaces\ModelHasRoleRepositoryInterface;

class ModelHasRoleRepository implements ModelHasRoleRepositoryInterface
{
    public function getTableName(): string
    {
        return 'model_has_roles';
    }

    public function delete(User $user): void
    {
        DB::table($this->getTableName())
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->delete();
    }

    public function create(User $user, string $role): self
    {
        DB::table($this->getTableName())
            ->insert([
                'model_id' => $user->id,
                'model_type' => get_class($user),
                'role_id' => Role::where('name', $role)->first()->id,
            ]);
            
        return $this;
    }
}