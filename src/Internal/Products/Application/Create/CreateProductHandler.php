<?php

namespace Internal\Products\Application\Create;

use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class CreateProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function handle(array $productRequest): int
    {
        if ($this->productRepository->existSku($productRequest['sku'])) {
            throw new BusinessLogicException(
                errors: ['sku' => 'El SKU ya está registrado'],
                message: 'Error de validación'
            );
        }

        return $this->productRepository->create($productRequest);
    }
}
