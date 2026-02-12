<?php

namespace Internal\Clients\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function getTableName(): string
    {
        return 'clients';
    }

    public function findById(int $id): object|null
    {
        $client = DB::table($this->getTableName())
            ->select('id', 'name', 'email', 'phone', 'status', 'tags', 'created_at', 'updated_at')
            ->where('id', $id)
            ->first();

        if (!$client) {
            return null;
        }

        return $client;
    }

    public function existById(int $id): bool
    {
        return DB::table($this->getTableName())
            ->where('id', $id)
            ->exists();
    }

    public function existEmail(string $email): bool
    {
        return DB::table($this->getTableName())
            ->where('email', $email)
            ->exists();
    }

    public function create(array $client): int
    {
        $tags = $client['tags'] ?? null;
        $tags = is_array($tags) ? json_encode($tags, JSON_UNESCAPED_UNICODE) : $tags;

        return DB::table($this->getTableName())
            ->insertGetId(
                [
                    'name' => $client['name'],
                    'email' => $client['email'],
                    'phone' => $client['phone'],
                    'status' => $client['status'],
                    'tags' => $tags,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }

    public function update(int $id, array $client): bool
    {
        $tags = $client['tags'] ?? null;
        $tags = is_array($tags) ? json_encode($tags, JSON_UNESCAPED_UNICODE) : $tags;

        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->update(
                [
                    'name' => $client['name'],
                    'email' => $client['email'],
                    'phone' => $client['phone'],
                    'status' => $client['status'],
                    'tags' => $tags,
                    'updated_at' => now(),
                ]
            );

        return $affected > 0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAll(?array $filters = []): array
    {
        $query = DB::table($this->getTableName())
            ->select('id', 'name', 'email', 'phone', 'status', 'tags', 'created_at', 'updated_at');

        if (!empty($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }

        return $query->orderBy('id', 'desc')->get()->toArray();
    }

    public function delete(int $id): bool
    {
        $affected = DB::table($this->getTableName())
            ->where('id', $id)
            ->delete();

        return $affected > 0;
    }
}

