<?php

namespace Internal\Clients\Test\Application;

use Internal\Clients\Application\Delete\DeleteClientHandler;
use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class DeleteClientHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ClientRepositoryInterface&LegacyMockInterface */
    private ClientRepositoryInterface $clientRepository;
    private DeleteClientHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ClientRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ClientRepositoryInterface::class);
        $this->clientRepository = $mock;
        $this->handler = new DeleteClientHandler($this->clientRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_throws_404_when_client_not_found(): void
    {
        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(999)
            ->andReturn(false);

        $mockRepository->shouldNotReceive('delete');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Cliente 999 no encontrado');
        $this->handler->handle(999);
    }

    public function test_it_deletes_a_client_successfully(): void
    {
        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(1)
            ->andReturn(true);

        $mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->handler->handle(1);
        $this->assertTrue($result);
    }
}

