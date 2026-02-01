<?php

namespace Internal\Users\Application\Update;

use Illuminate\Support\Facades\Hash;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Illuminate\Http\Request;

class UpdateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request, int $id): bool
    {
        $existingUser = $this->userRepository->findById($id);
        
        if ($existingUser === null) {
            throw new BusinessLogicException(
                message: 'Usuario no encontrado',
                code: 404
            );
        }

        if ($request->input('email') !== $existingUser['email']) {
            $userWithEmail = $this->userRepository->findByEmail($request->input('email'));
            
            if ($userWithEmail !== null && $userWithEmail['id'] !== $id) {
                throw new BusinessLogicException(
                    ['email' => 'El correo ya está registrado'],
                    'Email duplicado'
                );
            }
        }

        $data = $request->only(['name', 'email', 'role', 'status']);


        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $result = $this->userRepository->update($id, $data);
        
        if (!$result) {
            throw new BusinessLogicException(
                message: "Error al actualizar usuario {$id}",
                code:500
            );
        }
        
        return true;
    }
}

