<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\UserLogin;
use App\Exceptions\InvalidCredentialsException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogin()
    {
        $user = User::factory()->create(['password' => 'correct_pass']);

        $user = UserLogin::execute(['email' => $user->email, 'password' => 'correct_pass']);

        $this->actingAs($user);

        $this->assertTrue(Auth::check());
    }

    public function testUserLoginInvalidCredentials()
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->expectException(InvalidCredentialsException::class);

        UserLogin::execute(['email' => $user->email, 'password' => 'wrong_password']);
    }
}
