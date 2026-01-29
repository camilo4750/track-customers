<?php

namespace Internal\Users\Test\Application;

use Internal\Users\Application\Update\UpdateUserHandler;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;
use Internal\Shared\Exceptions\BusinessLogicException;

class UpdateUserHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var UserRepositoryInterface&LegacyMockInterface */
    private UserRepositoryInterface $userRepository;
    private UpdateUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(UserRepositoryInterface::class);
        $this->userRepository = $mock;
        $this->handler = new UpdateUserHandler($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_updates_a_user_successfully(): void
    {
        // Arrange
        $request = (object) [
            'id' => 1,
            'name' => 'John Updated',
            'email' => 'john.doe@example.com',
            'password' => null,
            'role' => 'user',
            'status' => 'active',
        ];

        $existingUser = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'active',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($existingUser);

        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, Mockery::on(function ($arg) {
                return is_array($arg)
                    && $arg['name'] === 'John Updated'
                    && $arg['email'] === 'john.doe@example.com';
            }))
            ->andReturn(true);

        // Act
        $result = $this->handler->handle($request);

        // Assert
        $this->assertTrue($result);
    }

    public function test_it_updates_user_password(): void
    {
        // Arrange
        $request = (object) [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'newpassword123',
            'role' => 'user',
            'status' => 'active',
        ];

        $existingUser = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($existingUser);

        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, Mockery::on(function ($arg) {
                return is_array($arg) && isset($arg['password']);
            }))
            ->andReturn(true);

        // Act
        $result = $this->handler->handle($request);

        // Assert
        $this->assertTrue($result);
    }
}

