<?php

namespace Internal\Clients\Test\Application;

use Internal\Clients\Application\Create\CreateClientHandler;
use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class CreateClientHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ClientRepositoryInterface&LegacyMockInterface */
    private ClientRepositoryInterface $clientRepository;
    private CreateClientHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ClientRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ClientRepositoryInterface::class);
        $this->clientRepository = $mock;
        $this->handler = new CreateClientHandler($this->clientRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_a_client_successfully(): void
    {
        $clientRequest = [
            'name' => 'Acme Inc',
            'email' => 'contact@acme.test',
            'phone' => '123',
            'status' => 'active',
            'tags' => ['vip', 'north'],
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('existEmail')
            ->once()
            ->with('contact@acme.test')
            ->andReturn(false);

        $mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($clientRequest)
            ->andReturn(10);

        $id = $this->handler->handle($clientRequest);

        $this->assertSame(10, $id);
    }

    public function test_it_creates_with_required_fields(): void
    {
        $clientRequest = [
            'name' => 'Acme Inc',
            'email' => 'contact@acme.test',
            'phone' => '123',
            'status' => 'active',
            'tags' => [],
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('existEmail')
            ->once()
            ->with('contact@acme.test')
            ->andReturn(false);

        $mockRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return is_array($arg)
                    && $arg['status'] === 'active'
                    && $arg['name'] === 'Acme Inc';
            }))
            ->andReturn(1);

        $id = $this->handler->handle($clientRequest);

        $this->assertSame(1, $id);
    }

    public function test_it_throws_when_email_already_exists(): void
    {
        $clientRequest = [
            'name' => 'Acme Inc',
            'email' => 'existing@acme.test',
            'phone' => '123',
            'status' => 'active',
            'tags' => [],
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->clientRepository;
        $mockRepository
            ->shouldReceive('existEmail')
            ->once()
            ->with('existing@acme.test')
            ->andReturn(true);

        $mockRepository->shouldNotReceive('create');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Error de validación');
        $this->handler->handle($clientRequest);
    }
}

