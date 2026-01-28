<?php

namespace Internal\Users\Test\Infrastructure;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Internal\Users\Infrastructure\Repositories\UserRepository;
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

    public function test_it_can_create_a_user(): void
    {
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'password123',
        ];

        $userId = $this->repository->create($userData);

        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ]);
    }

    public function test_it_can_find_user_by_email(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $userId = $this->repository->create($userData);

        $foundUser = $this->repository->findByEmail('john.doe@example.com');

        $this->assertIsArray($foundUser);
        $this->assertEquals($userId, $foundUser['id']);
        $this->assertEquals('john.doe@example.com', $foundUser['email']);
        $this->assertEquals('John Doe', $foundUser['name']);
    }

    public function test_it_returns_null_when_user_not_found_by_email(): void
    {
        $foundUser = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($foundUser);
    }

    public function test_it_can_find_all_users(): void
    {
        $user1 = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $user2 = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'password123',
        ];

        $created1 = $this->repository->create($user1);
        $created2 = $this->repository->create($user2);

        $users = $this->repository->findAll();

        $this->assertIsArray($users);
        $this->assertCount(2, $users);
        $this->assertInstanceOf(\stdClass::class, $users[0]);
        $this->assertInstanceOf(\stdClass::class, $users[1]);

        // Orden descendente por id: último creado primero
        $this->assertEquals('jane.doe@example.com', $users[0]->email);
        $this->assertEquals('john.doe@example.com', $users[1]->email);
    }

    public function test_it_can_find_user_by_id(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $userId = $this->repository->create($userData);

        $foundUser = $this->repository->findById($userId);

        $this->assertIsArray($foundUser);
        $this->assertEquals($userId, $foundUser['id']);
        $this->assertEquals('john.doe@example.com', $foundUser['email']);
        $this->assertEquals('John Doe', $foundUser['name']);
    }
    
    public function test_it_returns_null_when_user_not_found_by_id(): void
    {
        $foundUser = $this->repository->findById(999);

        $this->assertNull($foundUser);
    }

    public function test_it_can_update_a_user(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $userId = $this->repository->create($userData);

        $updated = $this->repository->update($userId, [
            'name' => 'John Updated',
            'email' => 'john.updated@example.com',
            'password' => 'newpassword123',
            'status' => 'active',
        ]);

        $this->assertTrue($updated);

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'name' => 'John Updated',
            'email' => 'john.updated@example.com',
            'status' => 'active',
        ]);
    }

    public function test_it_can_delete_a_user(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $userId = $this->repository->create($userData);

        $this->assertDatabaseHas('users', ['id' => $userId]);

        $deleted = $this->repository->delete($userId);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_it_returns_false_when_deleting_nonexistent_user(): void
    {
        $deleted = $this->repository->delete(999);

        $this->assertFalse($deleted);
    }
}