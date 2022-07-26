<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\AuthControllerJWT;
use App\Http\Controllers\API\HomePage\CategoryController;
use App\Http\Controllers\API\HomePage\DiscountController;
use App\Http\Controllers\API\HomePage\ProductController;
use App\Http\Controllers\API\HomePage\RestaurantController;
use App\Http\Controllers\API\HomePage\SearchFilterController;
use App\Http\Controllers\API\HomePage\SubCategoryController;
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

    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::post('/logout', [AuthControllerJWT::class, 'logout']);
        Route::post('/refresh', [AuthControllerJWT::class, 'refresh']);
        Route::get('/user-profile', [AuthControllerJWT::class, 'userProfile']);

    });

    Route::post('/login', [AuthControllerJWT::class, 'login']);
    Route::post('/register', [AuthControllerJWT::class, 'register']);


    Route::post('password/email', ForgotPasswordController::class);
    Route::post('password/code/check', CodeCheckController::class);
    Route::post('password/reset', ResetPasswordController::class);

//    Route::resource('restaurants', RestaurantController::class);
    Route::get('restaurants', [RestaurantController::class, 'index']);
    Route::get('restaurants/{id}', [RestaurantController::class, 'show']);
    Route::post('restaurants', [RestaurantController::class, 'store']);
    Route::post('restaurants/{id}', [RestaurantController::class, 'update']);
    Route::delete('restaurants/{id}', [RestaurantController::class, 'destroy']);

//    Route::resource('categories', CategoryController::class);


    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::post('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

//    Route::resource('products', ProductController::class);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store']);
    Route::post('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

//    Route::resource('subcategories', SubCategoryController::class);
    Route::get('subcategories', [SubCategoryController::class, 'index']);
    Route::get('subcategories/{id}', [SubCategoryController::class, 'show']);
    Route::post('subcategories', [SubCategoryController::class, 'store']);
    Route::post('subcategories/{id}', [SubCategoryController::class, 'update']);
    Route::delete('subcategories/{id}', [SubCategoryController::class, 'destroy']);


//    Route::post('/login', [AuthController::class, 'login']);
//    Route::post('/register', [AuthController::class, 'register']);

//    Route::resource('discounts', DiscountController::class);
    Route::get('product/discounts/{id}', [DiscountController::class, 'showProductOffer']);
    Route::get('discounts', [DiscountController::class, 'index']);
    Route::get('discounts/{id}', [DiscountController::class, 'show']);
    Route::post('discounts', [DiscountController::class, 'store']);
    Route::post('discounts/{id}', [DiscountController::class, 'update']);
    Route::delete('discounts/{id}', [DiscountController::class, 'destroy']);

    //Search && Filter
    Route::get('search', [SearchFilterController::class, 'Search']);
    Route::get('filter', [SearchFilterController::class, 'Filter']);


});
