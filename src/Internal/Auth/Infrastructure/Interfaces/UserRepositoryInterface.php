<?php
namespace Internal\Auth\Infrastructure\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email):?User;
}