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
            'password' => 'newpassword123',
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

    public function test_it_throws_exception_when_user_not_found(): void
    {
        // Arrange
        $request = (object) [
            'id' => 999,
            'name' => 'John Updated',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $mockRepository
            ->shouldNotReceive('update');

        // Assert
        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Usuario no encontrado');
        $this->expectExceptionCode(404);

        // Act
        $this->handler->handle($request);
    }

    public function test_it_throws_exception_when_email_already_exists(): void
    {
        // Arrange
        $request = (object) [
            'id' => 1,
            'email' => 'existing@example.com',
        ];

        $existingUser = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        $userWithEmail = [
            'id' => 2,
            'name' => 'Jane Doe',
            'email' => 'existing@example.com',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($existingUser);

        $mockRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('existing@example.com')
            ->andReturn($userWithEmail);

        $mockRepository
            ->shouldNotReceive('update');

        // Assert
        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Email duplicado');

        // Act
        $this->handler->handle($request);
    }

    public function test_it_allows_same_email_for_same_user(): void
    {
        // Arrange
        $request = (object) [
            'id' => 1,
            'name' => 'John Updated',
            'email' => 'john.doe@example.com', // Mismo email
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

        // No debe verificar email porque es el mismo
        $mockRepository
            ->shouldNotReceive('findByEmail');

        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->handler->handle($request);

        // Assert
        $this->assertTrue($result);
    }

    public function test_it_throws_exception_when_no_data_to_update(): void
    {
        // Arrange
        $request = (object) [
            'id' => 1,
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
            ->with(1, [
                'name' => null,
                'email' => null,
                'password' => null,
                'status' => null,
            ])
            ->andReturn(false);

        // Assert
        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Error al actualizar usuario');
        $this->expectExceptionCode(500);

        // Act
        $this->handler->handle($request);
    }
}

