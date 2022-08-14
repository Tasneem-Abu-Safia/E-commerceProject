<?php

use App\Http\Controllers\API\HomePage\ProductController;
use App\Http\Controllers\API\HomePage\RestaurantController;
use App\Http\Controllers\Web\DashBoard\CategoryController;
use App\Http\Controllers\Web\DashBoard\LocalizationController;
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


//Route::group(['middleware' => ['auth']], function () {
//
//});
//

Route::get('/', function () {
    return view('welcome');
});

