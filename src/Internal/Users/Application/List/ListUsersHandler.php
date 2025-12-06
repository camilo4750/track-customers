<?php

namespace Internal\Users\Application\List;

use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;

class ListUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(): array
    {
        return $this->userRepository->findAll();
    }
}

