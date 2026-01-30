<?php

namespace Internal\Users\Application\Delete;

use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class DeleteUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(int $id): bool
    {
        $existingUser = $this->userRepository->findById($id);

        if ($existingUser === null) {
            throw new BusinessLogicException(
                message: 'Usuario no encontrado',
                code: 404
            );
        }

        $result = $this->userRepository->delete($id);

        if (!$result) {
            throw new BusinessLogicException(
                message: 'Error al eliminar usuario',
                code: 404
            );
        }

        return true;
    }
}
