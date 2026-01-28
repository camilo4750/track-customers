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
        $data = [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => Hash::make($user['password']),
            'role' => $user['role'],
            'status' => $user['status'] ?? 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return DB::table($this->getTableName())
            ->insertGetId($data);
    }

    public function update(int $id, array $user): bool
    {
        $data = [
            'name' => $user['name'],
            'email' => $user['email'],
            'status' => $user['status'],
            'role' => $user['role'],
            'updated_at' => now(),
        ];

        if (isset($user['password']) && $user['password'] !== null) {
            $data['password'] = Hash::make($user['password']);
        }

        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->update($data);
            
        return $affected > 0;
    }

    /**
     * @return array<int, \stdClass>
     */
    public function findAll(?array $filters = []): array
    {
        $query = DB::table($this->getTableName());

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
