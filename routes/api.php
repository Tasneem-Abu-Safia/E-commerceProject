<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\AuthControllerJWT;
use App\Http\Controllers\API\HomePage\CategoryController;
use App\Http\Controllers\API\HomePage\ProductController;
use App\Http\Controllers\API\HomePage\RestaurantController;
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

    Route::resource('restaurants', RestaurantController::class);
//    Route::get('restaurants', [RestaurantController::class,'index']);
//    Route::get('restaurants/{id}', [RestaurantController::class,'show']);
//    Route::post('restaurants', [RestaurantController::class,'store']);
//    Route::put('restaurants/{id}', [RestaurantController::class,'update']);
//    Route::delete('restaurants/{id}', [RestaurantController::class,'destroy']);

    Route::resource('categories', CategoryController::class);


//    Route::get('categories', [CategoryController::class,'index']);
//    Route::get('categories/{id}', [CategoryController::class,'show']);
//    Route::post('categories', [CategoryController::class,'store']);
//    Route::put('categories/{id}', [CategoryController::class,'update']);
//    Route::delete('categories/{id}', [CategoryController::class,'destroy']);

    Route::resource('products', ProductController::class);

//    Route::get('products', [ProductController::class,'index']);
//    Route::get('products/{id}', [ProductController::class,'show']);
//    Route::post('products', [ProductController::class,'store']);
//    Route::put('products/{id}', [ProductController::class,'update']);
//    Route::delete('products/{id}', [ProductController::class,'destroy']);

//    Route::post('/login', [AuthController::class, 'login']);
//    Route::post('/register', [AuthController::class, 'register']);


});
