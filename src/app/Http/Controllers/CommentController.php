<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        return $post->comments;
    }

    public function show(Post $post, Comment $comment)
    {
        return $comment;
    }
}
