<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\WebController;
use App\Http\Controllers\WEB\UsersController;

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

    Route::get('/dashboard', function () {return view('dashboard.dashboard');})->name("dashboard");
    Route::get('/users', [UsersController::class, 'index'])->name('users');


    //Route::get('/users', function () {return view('users.users');})->name("users");
    Route::get('/reporte1', function () {return view('reportes.reporte1');})->name("reporte1");
    Route::get('/reporte2', function () {return view('reportes.reporte2');})->name("reporte2");
    Route::get('/habitaciones', function () {return view('habitaciones.habitaciones');})->name("habitaciones");
    Route::get('/configuracion', function () {return view('configuracion.configuracion');})->name("configuracion");
});

Route::post('/logout', [WebController::class, 'logout'])->name('logout');

Route::get('/email', function () {return view('email');})->name("email");

