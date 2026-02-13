<?php

namespace Internal\Products\Test\Application;

use Internal\Products\Application\Update\UpdateProductHandler;
use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class UpdateProductHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ProductRepositoryInterface&LegacyMockInterface */
    private ProductRepositoryInterface $productRepository;
    private UpdateProductHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ProductRepositoryInterface&LegacyMockInterface $mock */
        $mock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productRepository = $mock;
        $this->handler = new UpdateProductHandler($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_throws_404_when_product_not_found(): void
    {
        $productRequest = [
            'name' => 'Producto X',
            'sku' => 'SKU-X',
            'price' => 10.00,
            'category' => null,
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(999)
            ->andReturn(false);

        $mockRepository->shouldNotReceive('update');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Producto 999 no encontrado');
        $this->handler->handle($productRequest, 999);
    }

    public function test_it_throws_when_sku_already_used_by_another(): void
    {
        $productRequest = [
            'name' => 'Producto Updated',
            'sku' => 'SKU-OTHER',
            'price' => 50.00,
            'category' => 'Electrónica',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(1)
            ->andReturn(true);

        $mockRepository
            ->shouldReceive('existSku')
            ->once()
            ->with('SKU-OTHER', 1)
            ->andReturn(true);

        $mockRepository->shouldNotReceive('update');

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Error de validación');
        $this->handler->handle($productRequest, 1);
    }

    public function test_it_updates_a_product_successfully(): void
    {
        $productRequest = [
            'name' => 'Producto Updated',
            'sku' => 'SKU-UPDATED',
            'price' => 79.99,
            'category' => 'Hogar',
        ];

        /** @var LegacyMockInterface $mockRepository */
        $mockRepository = $this->productRepository;
        $mockRepository
            ->shouldReceive('existById')
            ->once()
            ->with(1)
            ->andReturn(true);

        $mockRepository
            ->shouldReceive('existSku')
            ->once()
            ->with('SKU-UPDATED', 1)
            ->andReturn(false);

        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, Mockery::on(function ($arg) {
                return is_array($arg)
                    && $arg['name'] === 'Producto Updated'
                    && $arg['sku'] === 'SKU-UPDATED'
                    && $arg['price'] === 79.99
                    && $arg['category'] === 'Hogar';
            }))
            ->andReturn(true);

        $result = $this->handler->handle($productRequest, 1);
        $this->assertTrue($result);
    }
}
