<?php

namespace Internal\Clients\Test\Application;

use Internal\Clients\Application\List\ListClientHandler;
use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class ListClientHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ClientRepositoryInterface&LegacyMockInterface */
    private ClientRepositoryInterface $clientRepository;
    private ListClientHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ClientRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ClientRepositoryInterface::class);
        $this->clientRepository = $mock;
        $this->handler = new ListClientHandler($this->clientRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_clients(): void
    {
        $expected = [
            ['id' => 2, 'name' => 'B'],
            ['id' => 1, 'name' => 'A'],
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('findAll')
            ->once()
            ->with([])
            ->andReturn($expected);

        $result = $this->handler->handle();

        $this->assertSame($expected, $result);
    }

    public function test_it_passes_filters_to_repository(): void
    {
        $expected = [];
        $requestFilters = ['status' => 'active', 'search' => 'acme'];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('findAll')
            ->once()
            ->with(['status' => 'active'])
            ->andReturn($expected);

        $result = $this->handler->handle($requestFilters);

        $this->assertSame($expected, $result);
    }
}

