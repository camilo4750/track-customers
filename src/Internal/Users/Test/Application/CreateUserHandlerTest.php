<?php

namespace Internal\Users\Test\Application;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Internal\Users\Application\Create\CreateUserHandler;
use Internal\Users\Infrastructure\Interfaces\ModelHasRoleRepositoryInterface;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Tests\TestCase;

class CreateUserHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var UserRepositoryInterface&LegacyMockInterface */
    private UserRepositoryInterface $userRepository;

    /** @var ModelHasRoleRepositoryInterface&LegacyMockInterface */
    private ModelHasRoleRepositoryInterface $modelHasRoleRepository;

    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(UserRepositoryInterface::class);
        $this->userRepository = $mock;

        /** @var ModelHasRoleRepositoryInterface&LegacyMockInterface $mockRole */
        $mockRole = Mockery::mock(ModelHasRoleRepositoryInterface::class);
        $this->modelHasRoleRepository = $mockRole;

        $this->handler = new CreateUserHandler($this->userRepository, $this->modelHasRoleRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_a_user_successfully(): void
    {
        // Arrange
        $request = Request::create('/dummy', 'POST', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active',
        ]);

        $user = new User();
        $user->id = 1;
        $user->name = 'John Doe';
        $user->email = 'john.doe@example.com';

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
                    && Hash::check('password123', $arg['password'])
                    && $arg['status'] === 'active';
            }))
            ->andReturn(1);

        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($user);

        /** @var LegacyMockInterface $mockRoleRepository */
        $mockRoleRepository = $this->modelHasRoleRepository;
        $mockRoleRepository
            ->shouldReceive('delete')
            ->once()
            ->with($user);
        $mockRoleRepository
            ->shouldReceive('create')
            ->once()
            ->with($user, 'user')
            ->andReturnSelf();

        // Act
        $resultId = $this->handler->handle($request);

        // Assert - ahora el handler retorna el id del usuario creado
        $this->assertIsInt($resultId);
        $this->assertEquals(1, $resultId);
    }

    public function test_it_throws_exception_when_email_already_exists(): void
    {
        // Arrange
        $request = Request::create('/dummy', 'POST', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'role' => 'admin',
            'status' => 'active',
        ]);

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