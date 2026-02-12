<?php

namespace Internal\Clients\Application\List;

use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;

class ListClientHandler
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {
    }

    private function processFilters(?array $requestFilters = []): array
    {
        $filters = [];

        if (!empty($requestFilters['status'])) {
            $filters['status'] = $requestFilters['status'];
        }
        
        return $filters;
    }

    public function handle(?array $requestFilters = []): array
    {
        $filters = $this->processFilters($requestFilters);
        return $this->clientRepository->findAll($filters);
    }
}

