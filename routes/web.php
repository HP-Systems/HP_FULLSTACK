<?php

use App\Http\Controllers\WEB\WebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});


Route::get('/login', function () {
    return view('login');
});


Route::get('/confirm/{id}', [WebController::class, 'confirmEmail'])->name('confirm')->where('id', '[0-9]+');
Route::get('/login', [WebController::class, 'loginForm'])->name('login.form');
Route::post('/login', [WebController::class, 'login'])->name('login');

