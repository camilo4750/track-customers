<?php

namespace Internal\Users\Application\Create;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Internal\Users\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Internal\Users\Infrastructure\Interfaces\ModelHasRoleRepositoryInterface;

class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ModelHasRoleRepositoryInterface $modelHasRoleRepository,
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
        $user = $request->only(['name', 'email', 'password', 'status']);
        $user['password'] = Hash::make($request->input('password'));
        $user['created_at'] = now();
        $user['updated_at'] = now();

        $userId = $this->userRepository->create($user);
        $this->syncRoles($userId, $request->input('role'));

        return $userId;
    }

    private function syncRoles(int $userId, string $role): void
    {  
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new BusinessLogicException(
                message: 'Usuario no encontrado',
                code: 404,
            );
        }
        $this->modelHasRoleRepository->delete($user);
        $this->modelHasRoleRepository->create($user, $role);
    }
}

