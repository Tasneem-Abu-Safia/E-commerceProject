<?php

use App\Http\Controllers\API\HomePage\CategoriesController;
use App\Http\Controllers\API\HomePage\RestaurantsController;
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
Route::get('restaurants/view', [RestaurantsController::class,'index']);

Route::post('category/store', [CategoriesController::class,'store']);
Route::post('restaurant/store', [RestaurantsController::class,'store']);
Route::delete('restaurant/delete/{id}', [RestaurantsController::class,'destroy']);
