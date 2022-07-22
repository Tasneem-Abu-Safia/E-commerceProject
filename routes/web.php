<?php

use App\Http\Controllers\API\HomePage\CategoryController;
use App\Http\Controllers\API\HomePage\ProductController;
use App\Http\Controllers\API\HomePage\RestaurantController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::resource('restaurants', RestaurantController::class);
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);


//Route::get('restaurants/view', [RestaurantController::class,'index']);
//Route::post('category/store', [CategoryController::class,'store']);
//Route::post('restaurant/store', [RestaurantController::class,'store']);
//Route::delete('restaurant/delete/{id}', [RestaurantController::class,'destroy']);
