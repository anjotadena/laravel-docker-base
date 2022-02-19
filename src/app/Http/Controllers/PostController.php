<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    //
    public function index()
    {
        return Post::all();
    }

    public function show(Post $post)
    {
        return $post;
    }
}
