<?php

namespace Internal\Products\Test\Application;

use Internal\Products\Application\Delete\DeleteProductHandler;
use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class DeleteProductHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ProductRepositoryInterface&LegacyMockInterface */
    private ProductRepositoryInterface $productRepository;
    private DeleteProductHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ProductRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productRepository = $mock;
        $this->handler = new DeleteProductHandler($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_throws_404_when_product_not_found(): void
    {
        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(999)
            ->andReturn(false);

        $mockRepository->shouldNotReceive('delete');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Producto 999 no encontrado');
        $this->handler->handle(999);
    }

    public function test_it_deletes_a_product_successfully(): void
    {
        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
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
