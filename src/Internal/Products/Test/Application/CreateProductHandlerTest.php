<?php

namespace Internal\Products\Test\Application;

use Internal\Products\Application\Create\CreateProductHandler;
use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class CreateProductHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ProductRepositoryInterface&LegacyMockInterface */
    private ProductRepositoryInterface $productRepository;
    private CreateProductHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ProductRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productRepository = $mock;
        $this->handler = new CreateProductHandler($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_a_product_successfully(): void
    {
        $productRequest = [
            'name' => 'Producto Test',
            'sku' => 'SKU-001',
            'price' => 29.99,
            'category' => 'Electrónica',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('existSku')
            ->once()
            ->with('SKU-001')
            ->andReturn(false);

        $mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($productRequest)
            ->andReturn(10);

        $id = $this->handler->handle($productRequest);

        $this->assertSame(10, $id);
    }

    public function test_it_creates_with_required_fields(): void
    {
        $productRequest = [
            'name' => 'Producto Test',
            'sku' => 'SKU-002',
            'price' => 99.99,
            'category' => null,
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('existSku')
            ->once()
            ->with('SKU-002')
            ->andReturn(false);

        $mockRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return is_array($arg)
                    && $arg['name'] === 'Producto Test'
                    && $arg['sku'] === 'SKU-002'
                    && $arg['price'] === 99.99;
            }))
            ->andReturn(1);

        $id = $this->handler->handle($productRequest);

        $this->assertSame(1, $id);
    }

    public function test_it_throws_when_sku_already_exists(): void
    {
        $productRequest = [
            'name' => 'Producto Test',
            'sku' => 'SKU-EXISTING',
            'price' => 19.99,
            'category' => 'Otros',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('existSku')
            ->once()
            ->with('SKU-EXISTING')
            ->andReturn(true);

        $mockRepository->shouldNotReceive('create');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Error de validación');
        $this->handler->handle($productRequest);
    }
}
