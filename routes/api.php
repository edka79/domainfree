<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NobodyController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


//Route::middleware('auth:api')->group(function () {
    Route::get('nobody', [ NobodyController::class, 'index' ]);
    Route::get('home', [ HomeController::class, 'home' ])->name('home');
    Route::resource('favorite', \App\Http\Controllers\FavoriteController::class);
    Route::get('/searching', [\App\Http\Controllers\SearchController::class, 'index']);
//});
