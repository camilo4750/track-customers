<?php

namespace Internal\Users\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Internal\Shared\Http\ControllerWrapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Internal\Users\Application\Create\CreateUserHandler;
use Internal\Users\Application\Delete\DeleteUserHandler;
use Internal\Users\Application\List\ListUsersHandler;
use Internal\Users\Application\Update\UpdateUserHandler;
use Internal\Users\Infrastructure\Http\Controllers\Requests\CreateUserRequest;
use Internal\Users\Infrastructure\Http\Controllers\Requests\UpdateUserRequest;
use Internal\Users\Application\Permission\ListUserPermissionsHandler;
use Internal\Users\Application\Permission\PermissionUserHandler;

class UserController extends Controller
{
    public function __construct(
        private CreateUserHandler $createUserHandler,
        private DeleteUserHandler $deleteUserHandler,
        private ListUsersHandler $listUsersHandler,
        private UpdateUserHandler $updateUserHandler,
        private ListUserPermissionsHandler $listUserPermissionsHandler,
        private PermissionUserHandler $permissionUserHandler
    ) {
    }

    public function index()
    {
        return Inertia::render('users/pages/User');
    }

    public function indexApi(): array|JsonResponse {
        return ControllerWrapper::execWithJsonSuccessResponse(function () {
            $requestFilters = request()->only(['role', 'status']);
            $users = $this->listUsersHandler->handle($requestFilters);
            return [
                'message' => 'Usuarios listados correctamente',
                'data' => $users
            ];
        });
    }

    public function store(Request $request): array|JsonResponse              
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request) {
            (new CreateUserRequest())
                ->validateRequest($request);

            $userId = $this->createUserHandler->handle($request);

            return [
                'message' => "Usuario {$userId} Creado correctamente"
            ];
        });
    }

    public function update(Request $request, int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request, $id) {
            (new UpdateUserRequest())
                ->validateRequest($request, $id);

            $this->updateUserHandler->handle($request, $id);
      
            return [
                'message' => "Usuario {$id} actualizado correctamente"
            ];
        });
    }

    public function destroy(int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($id) {
            $this->deleteUserHandler->handle($id);
            return [
                'message' => "Usuario {$id} eliminado correctamente"
            ];
        });
    }

    public function permissions(int $id)
    {
        return Inertia::render('users/pages/UserPermissions', ['id' => $id]);
    }

    public function permissionsApi(int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($id) {
            $data = $this->listUserPermissionsHandler->handle(request(), $id);
            return [
                'message' => "Permisos listados correctamente",
                'data' => $data
            ];
        });
    }

    public function syncPermissions(Request $request, int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request, $id) {
        $this->permissionUserHandler->handle($request, $id);
        return [
            'message' => "Permisos sincronizados correctamente"
        ];
    });
}
}
