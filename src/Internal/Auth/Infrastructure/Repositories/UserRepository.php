<?php
namespace Internal\Auth\Infrastructure\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Internal\Auth\Infrastructure\Interfaces\UserRepositoryInterface;
class UserRepository implements UserRepositoryInterface
{
    public function getTableName(): string
    {
        return 'users';
    }

    public function findByEmail(string $email):?User
    {
        return User::query()
            ->select('id', 'name', 'email', 'password', 'status')
            ->where('email', $email)
            ->first();
    }
}
