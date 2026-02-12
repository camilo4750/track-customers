<?php

namespace Internal\Clients\Test\Application;

use Internal\Clients\Application\Update\UpdateClientHandler;
use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class UpdateClientHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ClientRepositoryInterface&LegacyMockInterface */
    private ClientRepositoryInterface $clientRepository;
    private UpdateClientHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ClientRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ClientRepositoryInterface::class);
        $this->clientRepository = $mock;
        $this->handler = new UpdateClientHandler($this->clientRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_throws_404_when_client_not_found(): void
    {
        $clientRequest = ['name' => 'X', 'email' => 'x@test.com', 'phone' => '1', 'status' => 'active', 'tags' => []];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(999)
            ->andReturn(false);

        $mockRepository->shouldNotReceive('update');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Cliente 999 no encontrado');
        $this->handler->handle($clientRequest, 999);
    }

    public function test_it_updates_a_client_successfully(): void
    {
        $clientRequest = [
            'name' => 'Acme Updated',
            'email' => 'acme@test.com',
            'phone' => '456',
            'status' => 'inactive',
            'tags' => ['vip'],
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(1)
            ->andReturn(true);

        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, Mockery::on(function ($arg) {
                return is_array($arg)
                    && $arg['name'] === 'Acme Updated'
                    && $arg['status'] === 'inactive'
                    && $arg['tags'] === ['vip'];
            }))
            ->andReturn(true);

        $result = $this->handler->handle($clientRequest, 1);
        $this->assertTrue($result);
    }
}

