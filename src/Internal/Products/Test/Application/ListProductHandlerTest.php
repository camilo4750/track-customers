<?php

namespace Internal\Products\Test\Application;

use Internal\Products\Application\List\ListProductHandler;
use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class ListProductHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ProductRepositoryInterface&LegacyMockInterface */
    private ProductRepositoryInterface $productRepository;
    private ListProductHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ProductRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productRepository = $mock;
        $this->handler = new ListProductHandler($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_products(): void
    {
        $expected = [
            ['id' => 2, 'name' => 'Producto B', 'sku' => 'SKU-B', 'price' => 50.00, 'category' => 'Otros'],
            ['id' => 1, 'name' => 'Producto A', 'sku' => 'SKU-A', 'price' => 29.99, 'category' => 'Electrónica'],
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
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
        $requestFilters = ['category' => 'Electrónica', 'search' => 'SKU-1'];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('findAll')
            ->once()
            ->with(['category' => 'Electrónica'])
            ->andReturn($expected);

        $result = $this->handler->handle($requestFilters);

        $this->assertSame($expected, $result);
    }
}
