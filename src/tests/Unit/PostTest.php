<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Post;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function testShowPosts()
    {
        $users = User::factory()->count(5)->hasPosts(2)->create();

        $this->assertDatabaseCount('users', 5);
    }
}
