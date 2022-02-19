<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->times(5)->create();

        $users->each(function ($user) {
            $posts = Post::factory()->times(100)->create(['user_id' => $user->id]);

            $posts->each(function ($post) use ($user) {
                Comment::factory()->times(10)->create(['post_id' => $post->id, 'user_id' => $user->id]);
            });
        });
    }
}
