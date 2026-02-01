<?php

namespace Internal\Auth\Test\Middleware;

use Illuminate\Http\Request;
use Internal\Auth\Infrastructure\Http\Middleware\JwtMiddleware;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class JwtMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var JWTAuth&LegacyMockInterface */
    private JWTAuth $jwt;

    private JwtMiddleware $middleware;

    /** @var object{id: int, status: string} */
    private object $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwt = Mockery::mock(JWTAuth::class);
        $this->middleware = new JwtMiddleware($this->jwt);
        $this->user = (object) ['id' => 1, 'status' => 'active'];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function isValidTokenWorking(): void
    {
        $this->jwt->shouldReceive('parseToken')->once()->andReturnSelf();
        $this->jwt->shouldReceive('authenticate')->once()->andReturn($this->user);

        $response = $this->middleware->handle(
            new Request(),
            fn () => response('OK')
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function isUserInactiveWorking(): void
    {
        $this->user->status = 'inactive';
        $this->jwt->shouldReceive('parseToken')->once()->andReturnSelf();
        $this->jwt->shouldReceive('authenticate')->once()->andReturn($this->user);

        $response = $this->middleware->handle(
            new Request(),
            fn () => response('OK')
        );

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('Usuario no autorizado', json_decode($response->getContent())->message);
    }

    #[Test]
    public function isInvalidTokenWorking(): void
    {
        $this->jwt->shouldReceive('parseToken')->once()->andReturnSelf();
        $this->jwt->shouldReceive('authenticate')->once()->andThrow(new TokenInvalidException());

        $response = $this->middleware->handle(
            new Request(),
            fn () => response('OK')
        );

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Token inválido', json_decode($response->getContent())->message);
    }

    #[Test]
    public function isExpiredTokenWorking(): void
    {
        $this->jwt->shouldReceive('parseToken')->once()->andReturnSelf();
        $this->jwt->shouldReceive('authenticate')->once()->andThrow(new TokenExpiredException());

        $response = $this->middleware->handle(
            new Request(),
            fn () => response('OK')
        );

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Token expirado', json_decode($response->getContent())->message);
    }

    #[Test]
    public function isJwtExceptionWorking(): void
    {
        $this->jwt->shouldReceive('parseToken')->once()->andReturnSelf();
        $this->jwt->shouldReceive('authenticate')->once()->andThrow(new JWTException('Token not found'));

        $response = $this->middleware->handle(
            new Request(),
            fn () => response('OK')
        );

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Token no encontrado', json_decode($response->getContent())->message);
    }

    #[Test]
    public function isExceptionWorking(): void
    {
        $this->jwt->shouldReceive('parseToken')->once()->andReturnSelf();
        $this->jwt->shouldReceive('authenticate')->once()->andThrow(new \Exception());

        $response = $this->middleware->handle(
            new Request(),
            fn () => response('OK')
        );

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Error al procesar el token', json_decode($response->getContent())->message);
    }
}
