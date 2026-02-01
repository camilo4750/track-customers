<?php

namespace Internal\Users\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        if (is_null($user)) {
            return null;
        }

        return (array) $user;
    }

    public function findById(int $id): ?array
    {
        $user = DB::table($this->getTableName())
            ->where('id', $id)
            ->first();

        if (is_null($user)) {
            return null;
        }

        return (array) $user;
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
            ->select('id', 'name', 'email', 'role', 'status', 'created_at', 'updated_at');

        if (!empty($filters)) {
            $query->where($filters);
        }

        return $query
        ->orderBy('id', 'desc')
        ->get()
        ->toArray();
    }

    public function delete(int $id): bool
    {
        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->delete();

        return $affected > 0;
    }
}
