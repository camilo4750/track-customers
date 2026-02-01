<?php

namespace Internal\Auth\Application\Auth;

use Internal\Auth\Infrastructure\Interfaces\UserRepositoryInterface;
use Tymon\JWTAuth\JWTAuth;
use Internal\Shared\Exceptions\BusinessLogicException;
use Illuminate\Support\Facades\Hash;
class AuthHandler 
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JWTAuth $jwt
    ) {
    }

    public function handle(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if(!$user) {
            throw new BusinessLogicException(
                ['email' => 'Usuario no encontrado'],
                'Usuario no encontrado',
                404
            );
        }
        if($user->status !== 'active') {
            throw new BusinessLogicException(
                ['email' => 'Usuario inactivo'],
                'Usuario inactivo',
            );
        }   
        if(!Hash::check($password, $user->password)) {
            throw new BusinessLogicException(
                ['email' => 'Contraseña incorrecta'],
                'Contraseña incorrecta',
            );
        }

        return [
            'access_token' => $this->jwt->fromUser($user),
            'expires_in' => $this->jwt->factory()->getTTL() * 60,
        ];
    }
}