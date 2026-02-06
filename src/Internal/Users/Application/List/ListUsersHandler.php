<?php

namespace Internal\Users\Application\List;

use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;

class ListUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    private function processFilters(array $requestFilters): array
    {
        $filters = [];

        if (isset($requestFilters['role']) && $requestFilters['role'] !== null && $requestFilters['role'] !== '') {
            $filters['roles.name'] = $requestFilters['role'];
        }

        if (isset($requestFilters['status']) && $requestFilters['status'] !== null && $requestFilters['status'] !== '') {
            $filters['status'] = $requestFilters['status'];
        }

        return $filters;
    }

    public function handle(?array $requestFilters = []): array
    {   
        $filters = $this->processFilters($requestFilters);
        return $this->userRepository->findAll($filters);
    }
}
