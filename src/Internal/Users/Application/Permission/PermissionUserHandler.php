<?php

namespace Internal\Users\Application\Permission;

use Illuminate\Http\Request;
use Internal\Shared\Exceptions\BusinessLogicException;
use Internal\Users\Infrastructure\Interfaces\ModelHasPermissionRepositoryInterface;
use App\Models\User;

class PermissionUserHandler
{
    public function __construct(
        private ModelHasPermissionRepositoryInterface $modelHasPermissionRepository
    )
    {
    }

    public function handle(Request $request, int $id): void
    {
        $user = $this->modelHasPermissionRepository->getUser($id);
        if ($user === null) {
            throw new BusinessLogicException(
                message: 'Usuario no encontrado',
                code: 404,
            );
        }
        
        $this->modelHasPermissionRepository->delete($user);

        foreach ($request->input('permission_ids') as $permission) {
            $this->modelHasPermissionRepository->create($user, $permission);
        }
    }
}