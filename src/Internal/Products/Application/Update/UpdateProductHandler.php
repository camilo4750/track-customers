<?php

namespace Internal\Products\Application\Update;

use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class UpdateProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function handle(array $productRequest, int $id): bool
    {
        $existingProduct = $this->productRepository->existById($id);

        if (!$existingProduct) {
            throw new BusinessLogicException(
                message: "Producto {$id} no encontrado",
                code: 404
            );
        }

        if ($this->productRepository->existSku($productRequest['sku'], $id)) {
            throw new BusinessLogicException(
                errors: ['sku' => 'El SKU ya está registrado por otro producto'],
                message: 'Error de validación'
            );
        }

        $result = $this->productRepository->update($id, $productRequest);

        if (!$result) {
            throw new BusinessLogicException(
                message: "Error al actualizar producto {$id}",
                code: 500
            );
        }

        return true;
    }
}
