<?php

namespace Internal\Clients\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Internal\Clients\Application\Create\CreateClientHandler;
use Internal\Clients\Application\Delete\DeleteClientHandler;
use Internal\Clients\Application\List\ListClientHandler;
use Internal\Clients\Application\Update\UpdateClientHandler;
use Internal\Clients\Infrastructure\Http\Controllers\Requests\CreateClientRequest;
use Internal\Clients\Infrastructure\Http\Controllers\Requests\UpdateClientRequest;
use Internal\Shared\Http\ControllerWrapper;

class ClientController extends Controller
{
    public function __construct(
        private CreateClientHandler $createClientHandler,
        private DeleteClientHandler $deleteClientHandler,
        private ListClientHandler $listClientHandler,
        private UpdateClientHandler $updateClientHandler,
    ) {
    }

    public function index()
    {
        return Inertia::render('clients/page/client');
    }

    public function indexApi(): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () {
            $requestFilters = request()->only(['status', 'search']);
            $clients = $this->listClientHandler
                ->handle($requestFilters);

            return [
                'message' => 'Clientes listados correctamente',
                'data' => $clients,
            ];
        });
    }

    public function store(Request $request): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request) {
            $createClientRequest = (new CreateClientRequest())
                ->validateRequest($request);

            $clientId = $this->createClientHandler
                ->handle($createClientRequest);

            return [
                'message' => "Cliente {$clientId} creado correctamente",
            ];
        });
    }

    public function update(Request $request, int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request, $id) {
            $updateClientRequest = (new UpdateClientRequest())
                ->validateRequest($request, $id);

            $this->updateClientHandler
                ->handle($updateClientRequest, $id);

            return [
                'message' => "Cliente {$id} actualizado correctamente",
            ];
        });
    }

    public function destroy(int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($id) {
            $this->deleteClientHandler->handle($id);

            return [
                'message' => "Cliente {$id} eliminado correctamente",
            ];
        });
    }
}

