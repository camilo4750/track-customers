<?php

namespace Internal\Seeders\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Internal\Seeders\Application\List\ListSeedersHandler;
use Internal\Seeders\Application\Run\RunSeederHandler;
use Internal\Seeders\Infrastructure\Http\Controllers\Requests\RunSeederRequest;
use Internal\Shared\Http\ControllerWrapper;

class SeederController extends Controller
{
    public function __construct(
        private ListSeedersHandler $listSeedersHandler,
        private RunSeederHandler $runSeederHandler,
    ) {
    }

    public function index()
    {
        return Inertia::render('seeders/pages/Seeders');
    }

    public function list(): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () {
            $seeders = $this->listSeedersHandler->handle();

            return [
                'message' => 'Seeders listados correctamente',
                'data' => $seeders,
            ];
        });
    }

    public function run(Request $request): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request) {
            $validated = (new RunSeederRequest())->validateRequest($request);
            $key = $validated['key'];
            $count = $validated['count'] ?? config("seeders.seeders.{$key}.defaultCount", 100);

            $result = $this->runSeederHandler->handle($key, (int) $count);

            return [
                'message' => $result['message'],
                'data' => ['created' => $result['created']],
            ];
        });
    }
}
