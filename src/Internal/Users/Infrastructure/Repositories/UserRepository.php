<?php

namespace Internal\Users\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getTableName(): string
    {
        return 'users';
    }

    public function findByEmail(string $email): ?array
    {
        $user = DB::table($this->getTableName())
            ->where('email', $email)
            ->first();

        if ($user === null) {
            return null;
        }

        return (array) $user;
    }

    public function findById(int $id): ?User
    {
        $user = User::select('id', 'name', 'email', 'status')->find($id);

        if ($user === null) {
            return null;
        }

        return $user;
    }

    public function create(array $user): int
    {
        return DB::table($this->getTableName())
            ->insertGetId($user);
    }

    public function update(int $id, array $user): bool
    {
        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->update($user);
            
        return $affected > 0;
    }

    /**
     * @return array<int, \stdClass>
     */
    public function findAll(?array $filters = []): array
    {
        $query = DB::table($this->getTableName())
            ->select('users.id', 'users.name', 'users.email', 'users.status', 'users.created_at', 'users.updated_at', 'roles.name as role')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id');

        if (!empty($filters)) {
            $query->where($filters);
        }

        
        return $query->orderBy('users.id', 'desc')->get()->toArray();
    }

    public function delete(int $id): bool
    {
        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->delete();

        return $affected > 0;
    }
}
