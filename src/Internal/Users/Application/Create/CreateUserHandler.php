<?php

namespace Internal\Users\Application\Create;

use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle($request): int
    {
        $existingUser = $this->userRepository->findByEmail($request->email);
        
        if ($existingUser !== null) {
            throw new BusinessLogicException(
                ['email' => 'El correo ya está registrado'],
                'Usuario duplicado'
            );
        }

        return $this->userRepository->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $request->role,
            'status'   => $request->status ?? 'active'
        ]);
    }
}

