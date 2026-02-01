<?php

namespace Internal\Users\Application\Create;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;

class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request): int
    {
        $existingUser = $this->userRepository->findByEmail($request->input('email'));
        
        if ($existingUser !== null) {
            throw new BusinessLogicException(
                ['email' => 'El correo ya está registrado'],
                'Usuario duplicado'
            );
        }
        $user = $request->only(['name', 'email', 'password', 'role', 'status']);
        $user['password'] = Hash::make($request->input('password'));

        return $this->userRepository->create($user);
    }
}

