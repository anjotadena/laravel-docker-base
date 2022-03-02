<?php

use App\Http\Controllers\API\v1\Auth\LoginController;
use App\Http\Controllers\API\v1\Auth\LogoutController;
use App\Http\Controllers\API\v1\Auth\RegisterController;
use App\Http\Controllers\API\v1\Auth\VerifyCodeController;
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

Route::namespace("Auth")->group(function () {
    Route::post('/login', LoginController::class)->name('auth.login');
    Route::post('/register', RegisterController::class)->name('auth.register');
    Route::post('/verify', VerifyCodeController::class)->name('auth.verify');
    

    Route::middleware('auth:sanctum')
        ->group(function () {
            Route::post('/logout', LogoutController::class)->name('auth.logout');      
        });

    // protected by is admin middelware
    Route::group(['middleware' => 'is_admin'], function () {
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
