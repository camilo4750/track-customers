<?php

namespace Internal\Products\Application\List;

use Internal\Products\Infrastructure\Interfaces\ProductRepositoryInterface;

class ListProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    private function processFilters(?array $requestFilters = []): array
    {
        $filters = [];

        if (!empty($requestFilters['category'])) {
            $filters['category'] = $requestFilters['category'];
        }

        return $filters;
    }

    public function handle(?array $requestFilters = []): array
    {
        $filters = $this->processFilters($requestFilters);
        return $this->productRepository->findAll($filters);
    }
}
