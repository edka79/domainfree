<?php

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
    return view('inner');
    return redirect()->route('login');
});

Route::get('/searching', [\App\Http\Controllers\SearchController::class, 'index']);

//Auth::routes();

Route::view('/search', 'inner');
Route::view('/favorite', 'inner');
Route::view('/free', 'inner');
Route::view('/alerts', 'inner');
Route::view('/drop', 'inner');
Route::view('/register', 'inner');
