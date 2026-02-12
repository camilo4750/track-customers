<?php

namespace Internal\Auth\Test\Application;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Internal\Auth\Application\Auth\AuthHandler;
use Internal\Auth\Infrastructure\Interfaces\UserRepositoryInterface;
use Internal\Shared\Exceptions\BusinessLogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tymon\JWTAuth\JWTAuth;

class AuthHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function isLoginWorking(): void
    {
        $userRepo = Mockery::mock(UserRepositoryInterface::class)
            ->shouldAllowMockingProtectedMethods();

        $userRepo->shouldReceive('findByEmail')
            ->once()
            ->andReturnUsing(function () {
                return $this->makeUser(1, 'Jhan Vega', 'jhan.vega@savne.net', Hash::make('password'), 'active');
            });

        $jwt = Mockery::mock(JWTAuth::class);
        $jwt->shouldReceive('fromUser')
            ->once()
            ->andReturn('fake-token');
        $jwt->shouldReceive('factory')
            ->once()
            ->andReturnUsing(function () {
                $factory = Mockery::mock();
                $factory->shouldReceive('getTTL')->once()->andReturn(60);
                return $factory;
            });

        $this->instance(UserRepositoryInterface::class, $userRepo);
        $this->instance(JWTAuth::class, $jwt);

        $records = App::make(AuthHandler::class)->handle('jhan.vega@savne.net', 'password');

        $this->assertArrayHasKey('access_token', $records);
        $this->assertArrayHasKey('expires_in', $records);
        $this->assertSame('fake-token', $records['access_token']);
        $this->assertSame(60, $records['expires_in']);
    }

    #[Test]
    public function isLoginWhenUserNullFailing(): void
    {
        $userRepo = Mockery::mock(UserRepositoryInterface::class)
            ->shouldAllowMockingProtectedMethods();

        $userRepo->shouldReceive('findByEmail')
            ->once()
            ->andReturnNull();

        $this->instance(UserRepositoryInterface::class, $userRepo);

        try {
            App::make(AuthHandler::class)->handle('jhan.vega@savne.net', 'password');
            $this->fail('Expected BusinessLogicException was not thrown');
        } catch (BusinessLogicException $exception) {
            $this->assertSame(404, $exception->getCode());
            $this->assertSame('Usuario no encontrado', $exception->getMessage());
        }
    }

    #[Test]
    public function isLoginWhenUserInactiveFailing(): void
    {
        $userRepo = Mockery::mock(UserRepositoryInterface::class)
            ->shouldAllowMockingProtectedMethods();

        $userRepo->shouldReceive('findByEmail')
            ->once()
            ->andReturnUsing(function () {
                return $this->makeUser(1, 'Jhan Vega', 'jhan.vega@savne.net', Hash::make('password'), 'inactive');
            });

        $this->instance(UserRepositoryInterface::class, $userRepo);

        try {
            App::make(AuthHandler::class)->handle('jhan.vega@savne.net', 'password');
            $this->fail('Expected BusinessLogicException was not thrown');
        } catch (BusinessLogicException $exception) {
            $this->assertSame('Usuario inactivo', $exception->getMessage());
            $this->assertSame(['email' => 'Usuario inactivo'], $exception->getErrors());
        }
    }

    #[Test]
    public function isLoginWhenPasswordWrongFailing(): void
    {
        $userRepo = Mockery::mock(UserRepositoryInterface::class)
            ->shouldAllowMockingProtectedMethods();

        $userRepo->shouldReceive('findByEmail')
            ->once()
            ->andReturnUsing(function () {
                return $this->makeUser(1, 'Jhan Vega', 'jhan.vega@savne.net', Hash::make('correct-password'), 'active');
            });

        $this->instance(UserRepositoryInterface::class, $userRepo);

        try {
            App::make(AuthHandler::class)->handle('jhan.vega@savne.net', 'wrong-password');
            $this->fail('Expected BusinessLogicException was not thrown');
        } catch (BusinessLogicException $exception) {
            $this->assertSame('Contraseña incorrecta', $exception->getMessage());
            $this->assertSame(['email' => 'Contraseña incorrecta'], $exception->getErrors());
        }
    }

    private function makeUser(int $id, string $name, string $email, string $password, string $status): User
    {
        $user = new User();
        $user->setRawAttributes([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'status' => $status,
        ]);
        return $user;
    }
}
