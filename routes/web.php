<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\WebController;

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


Route::get('/', function () {return view('login.login');})->name("login");
Route::post('/login', [WebController::class, 'login']);


Route::get('/verify', function () {return view('login.verify');})->middleware('signed')->name("verify");
Route::post('/verifyNumber', [WebController::class, 'verifyNumber']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', function () {return view('home');})->name("home");
});

Route::post('/logout', [WebController::class, 'logout'])->name('logout');

Route::get('/email', function () {return view('email');})->name("email");
