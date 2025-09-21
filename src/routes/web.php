<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Web\HomeController;
use Dedoc\Scramble\Scramble;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// API Documentation Route
Route::get('/api/docs', function () {
    return redirect('/docs/api');
});

Route::get('/api/docs/{path?}', function ($path = null) {
    return redirect('/docs/api' . ($path ? '/' . $path : ''));
})->where('path', '.*');

Route::get('/{any}', function () {
    return view('index');
})->where('any', '^(?!api|docs).*$');
