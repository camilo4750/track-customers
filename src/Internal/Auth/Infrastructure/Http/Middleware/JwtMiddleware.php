<?php
namespace Internal\Auth\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\JWTAuth;


class JwtMiddleware
{
    public function __construct(
        private JWTAuth $jwt
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = $this->jwt->parseToken()->authenticate();
            if($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autorizado',
                ], 403);
            }
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido',
            ], 401);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expirado',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token no encontrado',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el token',
            ], 500);
        }
        
        return $next($request);
    }
}