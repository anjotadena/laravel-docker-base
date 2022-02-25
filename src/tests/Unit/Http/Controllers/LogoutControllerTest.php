<?php

namespace Tests\Unit\Http\Controllers;

use App\Actions\Auth\UserLogin;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function resetAuth(array $guards = null)
    {
        $guards = $guards ?: array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            $guard = $this->app['auth']->guard($guard);

            if ($guard instanceof \Illuminate\Auth\SessionGuard) {
                $guard->logout();
            }
        }

        $protectedProperty = new \ReflectionProperty($this->app['auth'], 'guards');
        $protectedProperty->setAccessible(true);
        $protectedProperty->setValue($this->app['auth'], []);
    }

    public function testApiLogoutUser()
    {
        $user = User::factory()->create(['password' => 'password']);

        $user = UserLogin::execute(['email' => $user->email, 'password' => 'password']);

        $response = $this->postJson('/api/v1/logout', []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->resetAuth();

        $response = $this->postJson('/api/v1/logout', []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
