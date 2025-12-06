<?php

namespace Internal\Users\Test\Application;

use Internal\Users\Application\Create\CreateUserHandler;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;
use Internal\Shared\Exceptions\BusinessLogicException;

class CreateUserHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var UserRepositoryInterface&LegacyMockInterface */
    private UserRepositoryInterface $userRepository;
    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(UserRepositoryInterface::class);
        $this->userRepository = $mock;
        $this->handler = new CreateUserHandler($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_a_user_successfully(): void
    {
        // Arrange
        $request = (object) [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('john.doe@example.com')
            ->andReturn(null);

        $mockRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return is_array($arg)
                    && $arg['name'] === 'John Doe'
                    && $arg['email'] === 'john.doe@example.com'
                    && $arg['password'] === 'password123';
            }))
            ->andReturn(1); // ahora create devuelve el id (int)

        // Act
        $resultId = $this->handler->handle($request);

        // Assert - ahora el handler retorna el id del usuario creado
        $this->assertIsInt($resultId);
        $this->assertEquals(1, $resultId);
    }

    public function test_it_throws_exception_when_email_already_exists(): void
    {
        // Arrange
        $request = (object) [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $existingUser = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('john.doe@example.com')
            ->andReturn($existingUser);

        $mockRepository
            ->shouldNotReceive('create');

        // Assert

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Usuario duplicado');
        $this->handler->handle($request);
    }
}