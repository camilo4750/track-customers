<?php

namespace Internal\Users\Test\Application;

use Internal\Users\Application\List\ListUsersHandler;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Users\Test\Factories\UserFactory;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class ListUsersHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var UserRepositoryInterface&LegacyMockInterface */
    private UserRepositoryInterface $userRepository;
    private ListUsersHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(UserRepositoryInterface::class);
        $this->userRepository = $mock;
        $this->handler = new ListUsersHandler($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_all_users(): void
    {
        // Arrange
        $user1 = (object) UserFactory::make([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
        
        $user2 = (object) UserFactory::make([
            'id' => 2,
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ]);
        
        $expectedUsers = [$user1, $user2];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn($expectedUsers);

        // Act
        $result = $this->handler->handle();

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\stdClass::class, $result[0]);
        $this->assertInstanceOf(\stdClass::class, $result[1]);
        $this->assertEquals('john.doe@example.com', $result[0]->email);
        $this->assertEquals('jane.doe@example.com', $result[1]->email);
    }

    public function test_it_returns_empty_array_when_no_users_exist(): void
    {
        // Arrange
        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->userRepository;
        $mockRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        // Act
        $result = $this->handler->handle();

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}

