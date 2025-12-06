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
    public function findAll(): array
    {
        return DB::table($this->getTableName())
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

}
