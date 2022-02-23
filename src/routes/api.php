<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace("API")
    ->group(function () {
        Route::post('/login', [LoginController::class, 'store'])->name('auth.login');
        Route::post('/register', [RegisterController::class, 'store'])->name('auth.register');

        Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
            return $request->user();
        });
    });
