<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\AuthControllerJWT;
use App\Http\Controllers\API\HomePage\CategoriesController;
use App\Http\Controllers\API\HomePage\RestaurantsController;
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

    Route::post('password/email', ForgotPasswordController::class);
    Route::post('password/code/check', CodeCheckController::class);
    Route::post('password/reset', ResetPasswordController::class);

    Route::resource('restaurants', RestaurantsController::class);
//    Route::get('restaurants', [RestaurantsController::class,'index']);
//    Route::get('restaurants/{id}', [RestaurantsController::class,'show']);
//    Route::post('restaurants', [RestaurantsController::class,'store']);
//    Route::put('restaurants/{id}', [RestaurantsController::class,'update']);
//    Route::delete('restaurants/{id}', [RestaurantsController::class,'destroy']);

    Route::resource('categories', CategoriesController::class);


//    Route::get('categories', [CategoriesController::class,'index']);
//    Route::get('categories/{id}', [CategoriesController::class,'show']);
//    Route::post('categories', [CategoriesController::class,'store']);
//    Route::put('categories/{id}', [CategoriesController::class,'update']);
//    Route::delete('categories/{id}', [CategoriesController::class,'destroy']);

//    Route::post('/login', [AuthController::class, 'login']);
//    Route::post('/register', [AuthController::class, 'register']);


});
