<?php

namespace Internal\Users\Test\Application;

use Internal\Users\Application\Delete\DeleteUserHandler;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class DeleteUserHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var UserRepositoryInterface&LegacyMockInterface */
    private UserRepositoryInterface $userRepository;
    private DeleteUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(UserRepositoryInterface::class);
        $this->userRepository = $mock;
        $this->handler = new DeleteUserHandler($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_deletes_a_user_successfully(): void
    {
        $id = 1;
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
            ->with($id)
            ->andReturn($existingUser);

        $mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        $result = $this->handler->handle($id);

        $this->assertTrue($result);
    }
}
