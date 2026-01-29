<?php

namespace Internal\Users\Infrastructure\Http\Controllers;

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

class UserController extends Controller
{
    public function __construct(
        private CreateUserHandler $createUserHandler,
        private DeleteUserHandler $deleteUserHandler,
        private ListUsersHandler $listUsersHandler,
        private UpdateUserHandler $updateUserHandler
    ) {
    }

    public function index()
    {   
        $requestFilters = request()->only(['role', 'status']);
        $users = $this->listUsersHandler->handle($requestFilters);
        return Inertia::render('users/pages/User', [
            'data' => $users
        ]);
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

    public function store(CreateUserRequest $request): array|JsonResponse              
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request) {
            $userId = $this->createUserHandler->handle($request);

            return [
                'message' => "Usuario {$userId} Creado correctamente"
            ];
        });
    }

    public function update(UpdateUserRequest $request): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request) {
            $this->updateUserHandler->handle($request);

            return [
                'message' => "Usuario {$request->id} actualizado correctamente"
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
}
