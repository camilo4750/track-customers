<?php

namespace Internal\Clients\Application\Delete;

use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class DeleteClientHandler
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {
    }

    public function handle(int $id): bool
    {
        $existingClient = $this->clientRepository->existById($id);

        if (!$existingClient) {
            throw new BusinessLogicException(
                message: "Cliente {$id} no encontrado",
                code: 404
            );
        }

        $result = $this->clientRepository->delete($id);

        if (!$result) {
            throw new BusinessLogicException(
                message: "Error al eliminar cliente {$id}",
                code: 500
            );
        }

        return true;
    }
}

