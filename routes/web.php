<?php

use App\Http\Controllers\API\HomePage\CategoryController;
use App\Http\Controllers\API\HomePage\ProductController;
use App\Http\Controllers\API\HomePage\RestaurantController;
use Illuminate\Support\Facades\Auth;
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



Auth::routes();


    Route::group(['middleware' => ['guest']], function () {
        Route::get('home', function () {
            return view('home');
        });

});
