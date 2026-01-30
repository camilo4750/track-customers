<?php

namespace Internal\Auth\Test\Application;

use Internal\Auth\Application\Auth\AuthHandler;
use Internal\Auth\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;
use Tymon\JWTAuth\JWTAuth;

class AuthHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var UserRepositoryInterface&LegacyMockInterface */
    private UserRepositoryInterface $userRepository;

    /** @var JWTAuth&LegacyMockInterface */
    private JWTAuth $jwt;

    private AuthHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->jwt = Mockery::mock(JWTAuth::class);
        $this->handler = new AuthHandler($this->userRepository, $this->jwt);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_tokens_when_credentials_are_valid(): void
    {
        $email = 'john@example.com';
        $password = 'password123';
        $user = (object) [
            'id' => 1,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => 'active',
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($user);

        $this->jwt
            ->shouldReceive('fromUser')
            ->once()
            ->with($user)
            ->andReturn('fake-access-token');

        $factory = Mockery::mock();
        $factory->shouldReceive('getTTL')->once()->andReturn(60);
        $this->jwt->shouldReceive('factory')->once()->andReturn($factory);

        $result = $this->handler->handle($email, $password);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('expires_in', $result);
        $this->assertSame('fake-access-token', $result['access_token']);
        $this->assertSame(3600, $result['expires_in']);
    }

    public function test_it_throws_when_user_not_found(): void
    {
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('unknown@example.com')
            ->andReturn(null);

        $this->jwt->shouldNotReceive('fromUser');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Usuario no encontrado');

        $this->handler->handle('unknown@example.com', 'password123');
    }

    public function test_it_throws_when_user_is_inactive(): void
    {
        $user = (object) [
            'id' => 1,
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('inactive@example.com')
            ->andReturn($user);

        $this->jwt->shouldNotReceive('fromUser');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Usuario inactivo');

        $this->handler->handle('inactive@example.com', 'password123');
    }

    public function test_it_throws_when_password_is_wrong(): void
    {
        $user = (object) [
            'id' => 1,
            'email' => 'john@example.com',
            'password' => Hash::make('correct-password'),
            'status' => 'active',
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('john@example.com')
            ->andReturn($user);

        $this->jwt->shouldNotReceive('fromUser');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Contraseña incorrecta');

        $this->handler->handle('john@example.com', 'wrong-password');
    }
}
