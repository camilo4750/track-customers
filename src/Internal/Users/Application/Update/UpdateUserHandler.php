<?php

namespace Internal\Users\Application\Update;

use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class UpdateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle($request): bool
    {
        $existingUser = $this->userRepository->findById($request->id);
        
        if ($existingUser === null) {
            throw new BusinessLogicException(
                ['id' => 'Usuario no encontrado'],
                'Usuario no encontrado',
                404
            );
        }

        if (isset($request->email) && $request->email !== $existingUser['email']) {
            $userWithEmail = $this->userRepository->findByEmail($request->email);
            
            if ($userWithEmail !== null && $userWithEmail['id'] !== $request->id) {
                throw new BusinessLogicException(
                    ['email' => 'El correo ya está registrado'],
                    'Email duplicado'
                );
            }
        }

        // Preparar array con todos los campos usando ?? para valores por defecto
        $data = [
            'name' => $request->name ?? null,
            'email' => $request->email ?? null,
            'password' => $request->password ?? null,
            'status' => $request->status ?? null,
        ];

        $result = $this->userRepository->update($request->id, $data);
        
        if (!$result) {
            throw new BusinessLogicException(
                ['id' => 'No se pudo actualizar el usuario'],
                'Error al actualizar usuario',
                500
            );
        }
        
        return true;
    }
}

