<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\UserRegister;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister()
    {
        $user = UserRegister::execute([
            'name' => 'test user',
            'email' => 'user@test.com',
            'password' => 'password'
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => $user->email]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test user', $user->name);
        $this->assertEquals('user@test.com', $user->email);
        $this->assertNotEmpty($user->password);
    }
}
