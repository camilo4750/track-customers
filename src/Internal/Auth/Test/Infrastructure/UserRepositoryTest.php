<?php

namespace Internal\Auth\Test\Infrastructure;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Internal\Auth\Infrastructure\Repositories\UserRepository;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function test_get_table_name_returns_users(): void
    {
        $this->assertSame('users', $this->repository->getTableName());
    }

    public function test_find_by_email_returns_null_when_user_not_found(): void
    {
        $result = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($result);
    }
}