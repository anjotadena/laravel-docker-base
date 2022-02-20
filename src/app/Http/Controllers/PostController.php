<?php

namespace App\Http\Controllers;

use App\Jobs\GetFood;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function home()
    {
        if (Cache::store('redis')->has('posts')) {
            return Cache::get('posts');
        }

        $posts = Post::all();

        Cache::store('redis')->put('posts', $posts);
        
        return $posts;
    }

    //
    public function index()
    {
        return Post::all();
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function get()
    {
        GetFood::dispatch()->onQueue('food');

        return 'Added a job to queue';
    }
}
