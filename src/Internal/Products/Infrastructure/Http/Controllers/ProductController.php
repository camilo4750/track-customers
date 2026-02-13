<?php

namespace Internal\Products\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Internal\Products\Application\Create\CreateProductHandler;
use Internal\Products\Application\Delete\DeleteProductHandler;
use Internal\Products\Application\List\ListProductHandler;
use Internal\Products\Application\Update\UpdateProductHandler;
use Internal\Products\Infrastructure\Http\Controllers\Requests\CreateProductRequest;
use Internal\Products\Infrastructure\Http\Controllers\Requests\UpdateProductRequest;
use Internal\Shared\Http\ControllerWrapper;

class ProductController extends Controller
{
    public function __construct(
        private CreateProductHandler $createProductHandler,
        private DeleteProductHandler $deleteProductHandler,
        private ListProductHandler $listProductHandler,
        private UpdateProductHandler $updateProductHandler,
    ) {
    }

    public function index()
    {
        return Inertia::render('products/page/product');
    }

    public function indexApi(): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () {
            $requestFilters = request()->only(['category']);
            $products = $this->listProductHandler
                ->handle($requestFilters);

            return [
                'message' => 'Productos listados correctamente',
                'data' => $products,
            ];
        });
    }

    public function store(Request $request): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request) {
            $createProductRequest = (new CreateProductRequest())
                ->validateRequest($request);

            $productId = $this->createProductHandler
                ->handle($createProductRequest);

            return [
                'message' => "Producto {$productId} creado correctamente",
            ];
        });
    }

    public function update(Request $request, int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request, $id) {
            $updateProductRequest = (new UpdateProductRequest())
                ->validateRequest($request, $id);

            $this->updateProductHandler
                ->handle($updateProductRequest, $id);

            return [
                'message' => "Producto {$id} actualizado correctamente",
            ];
        });
    }

    public function destroy(int $id): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($id) {
            $this->deleteProductHandler->handle($id);

            return [
                'message' => "Producto {$id} eliminado correctamente",
            ];
        });
    }
}
