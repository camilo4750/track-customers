<?php

namespace Internal\Products\Application\Delete;

use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class DeleteProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function handle(int $id): bool
    {
        $existingProduct = $this->productRepository->existById($id);

        if (!$existingProduct) {
            throw new BusinessLogicException(
                message: "Producto {$id} no encontrado",
                code: 404
            );
        }

        $result = $this->productRepository->delete($id);

        if (!$result) {
            throw new BusinessLogicException(
                message: "Error al eliminar producto {$id}",
                code: 500
            );
        }

        return true;
    }
}
