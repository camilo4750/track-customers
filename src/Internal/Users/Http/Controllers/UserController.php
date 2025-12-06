<?php

namespace Internal\Users\Http\Controllers;

use App\Http\Controllers\Wrappers\ControllerWrapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Internal\Users\Application\Create\CreateUserHandler;
use Internal\Users\Application\List\ListUsersHandler;
use Internal\Users\Application\Update\UpdateUserHandler;
use Internal\Users\Http\Controllers\Requests\CreateUserRequest;
use Internal\Users\Http\Controllers\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(
        private CreateUserHandler $createUserHandler,
        private ListUsersHandler $listUsersHandler,
        private UpdateUserHandler $updateUserHandler
    ) {
    }

    public function index()
    {
        $users = $this->listUsersHandler->handle();
        return Inertia::render('Users/Index', [
            'data' => $users
        ]);
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
}

