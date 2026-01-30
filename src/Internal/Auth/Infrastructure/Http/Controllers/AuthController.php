<?php
namespace Internal\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Internal\Auth\Application\Auth\AuthHandler;
use Internal\Shared\Http\ControllerWrapper;
use Internal\Auth\Infrastructure\Http\Controllers\Requests\AuthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private AuthHandler $authHandler
    ) {
    }

    public function login(Request $request): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () use ($request) {
            (new AuthRequest())->validateRequest($request);

            $auth = $this->authHandler->handle(
                $request->input('email'),
                $request->input('password')
            );

            return [
                'email' => $request->input('email'),
                'token' => $auth['access_token'],
            ];
        });
    }

    public function logout(): array|JsonResponse
    {
        return ControllerWrapper::execWithJsonSuccessResponse(function () {
            Auth::guard('api')->logout();
            return [
                'message' => 'Sesión cerrada correctamente',
            ];
        });
    }
}