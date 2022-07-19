<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AuthControllerJWT;
use App\Http\Controllers\API\Passwords\CodeCheckController;
use App\Http\Controllers\API\Passwords\ForgotPasswordController;
use App\Http\Controllers\API\Passwords\ResetPasswordController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthControllerJWT::class, 'login']);
    Route::post('/register', [AuthControllerJWT::class, 'register']);
    Route::post('/logout', [AuthControllerJWT::class, 'logout']);
    Route::post('/refresh', [AuthControllerJWT::class, 'refresh']);
    Route::get('/user-profile', [AuthControllerJWT::class, 'userProfile']);

    Route::post('password/email',  ForgotPasswordController::class);
    Route::post('password/code/check', CodeCheckController::class);
    Route::post('password/reset', ResetPasswordController::class);
//    Route::post('/login', [AuthController::class, 'login']);
//    Route::post('/register', [AuthController::class, 'register']);
});
