<?php

namespace Internal\Users\Test\Application;

use App\Models\User;
use Illuminate\Http\Request;
use Internal\Users\Application\Update\UpdateUserHandler;
use Internal\Users\Infrastructure\Interfaces\ModelHasRoleRepositoryInterface;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Tests\TestCase;

class UpdateUserHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var UserRepositoryInterface&LegacyMockInterface */
    private UserRepositoryInterface $userRepository;

    /** @var ModelHasRoleRepositoryInterface&LegacyMockInterface */
    private ModelHasRoleRepositoryInterface $modelHasRoleRepository;

    private UpdateUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(UserRepositoryInterface::class);
        $this->userRepository = $mock;

        /** @var ModelHasRoleRepositoryInterface&LegacyMockInterface $mockRole */
        $mockRole = Mockery::mock(ModelHasRoleRepositoryInterface::class);
        $this->modelHasRoleRepository = $mockRole;

        $this->handler = new UpdateUserHandler($this->userRepository, $this->modelHasRoleRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_updates_a_user_successfully(): void
    {
        // Arrange
        $request = Request::create('/dummy', 'PUT', [
            'name' => 'John Updated',
            'email' => 'john.doe@example.com',
            'role' => 'user',
            'status' => 'active',
        ]);

        $existingUser = new User();
        $existingUser->id = 1;
        $existingUser->name = 'John Doe';
        $existingUser->email = 'john.doe@example.com';
        $existingUser->status = 'active';

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

        /** @var LegacyMockInterface $mockRoleRepository */
        $mockRoleRepository = $this->modelHasRoleRepository;
        $mockRoleRepository->shouldReceive('delete')->once()->with($existingUser);
        $mockRoleRepository->shouldReceive('create')->once()->with($existingUser, 'user')->andReturnSelf();

        // Act
        $result = $this->handler->handle($request, 1);

        // Assert
        $this->assertTrue($result);
    }

    public function test_it_updates_user_password(): void
    {
        // Arrange
        $request = Request::create('/dummy', 'PUT', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'newpassword123',
            'role' => 'user',
            'status' => 'active',
        ]);

        $existingUser = new User();
        $existingUser->id = 1;
        $existingUser->name = 'John Doe';
        $existingUser->email = 'john.doe@example.com';

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

        /** @var LegacyMockInterface $mockRoleRepository */
        $mockRoleRepository = $this->modelHasRoleRepository;
        $mockRoleRepository->shouldReceive('delete')->once()->with($existingUser);
        $mockRoleRepository->shouldReceive('create')->once()->with($existingUser, 'user')->andReturnSelf();

        // Act
        $result = $this->handler->handle($request, 1);

        // Assert
        $this->assertTrue($result);
    }
}

