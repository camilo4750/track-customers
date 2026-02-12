<?php

namespace Internal\Clients\Application\Update;

use Illuminate\Http\Request;
use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class UpdateClientHandler
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {
    }

    public function handle(array $clientRequest, int $id): bool
    {
        $existingClient = $this->clientRepository->existById($id);

        if (!$existingClient) {
            throw new BusinessLogicException(
                message: "Cliente {$id} no encontrado",
                code: 404
            );
        }

        $result = $this->clientRepository->update($id, $clientRequest);

        if (!$result) {
            throw new BusinessLogicException(
                message: "Error al actualizar cliente {$id}",
                code: 500
            );
        }

        return true;
    }
}

