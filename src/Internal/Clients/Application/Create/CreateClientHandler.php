<?php

namespace Internal\Clients\Application\Create;

use Internal\Clients\Infrastructure\Interfaces\ClientRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Illuminate\Http\Request;
class CreateClientHandler
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {
    }

    public function handle(array $clientRequest): int
    {
        if ($this->clientRepository->existEmail($clientRequest['email'])) {
            throw new BusinessLogicException(
                errors: ['email' => 'El email ya está registrado'],
                message: 'Error de validación'
            );
        }

        return $this->clientRepository->create($clientRequest);
    }


}

