<?php

namespace Internal\Users\Application\Permission;

use Illuminate\Http\Request;
use Internal\Shared\Exceptions\BusinessLogicException;
use Internal\Users\Infrastructure\Interfaces\PermissionRepositoryInterface;

class ListUserPermissionsHandler
{
    public function __construct(
        private PermissionRepositoryInterface $permissionRepository
    )
    {
    }

    public function handle(Request $request, int $id): array
    {
        $user = $this->permissionRepository->getUser($id);
        if ($user === null) {
            throw new BusinessLogicException(
                message: 'Usuario no encontrado',
                code: 404,
            );
        }
    
        $allPermissions = $this->permissionRepository->getPermissions();
        $byModule = $allPermissions->groupBy(fn ($p) => explode('.', $p->name)[0]);
        $permissionsByModule = $byModule->map(fn ($perms, $module) => $perms->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'action' => explode('.', $p->name)[1],
        ])->values()->all())->all();
    
        // dd($user->getDirectPermissions()->pluck('id')->values()->all());
        
        return [
            'userId' => $user->id,
            'userName' => $user->name,
            'permissionsByModule' => $permissionsByModule,
            'userPermissionIds' => $user->getDirectPermissions()->pluck('id')->values()->all(),
        ];
    }
}