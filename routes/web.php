<?php

use App\Http\Controllers\WEB\InfoHotelController;
use App\Http\Controllers\WEB\RoomController;
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


Route::get('/', function () {
    return auth()->check() ? redirect()->route('home') : view('login.login');
})->name("login");

Route::post('/login', [WebController::class, 'login']);


Route::get('/verify', function () {return view('login.verify');})->middleware('signed')->name("verify");
Route::post('/verifyNumber', [WebController::class, 'verifyNumber']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', function () {return view('home');})->name("home");

    Route::get('/dashboard', function () {return view('dashboard.dashboard');})->name("dashboard");
    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::post('/users/insert', [UsersController::class, 'insertPersonal'])->name('insertPersonal');
    Route::post('/users/edit', [UsersController::class, 'editPersonal'])->name('editPersonal');
    Route::post('/users/status', [UsersController::class, 'cambiarStatus'])->name('cambiarStatus');
   

    //Route::get('/users', function () {return view('users.users');})->name("users");
    Route::get('/reporte1', function () {return view('reportes.reporte1');})->name("reporte1");
    Route::get('/reporte2', function () {return view('reportes.reporte2');})->name("reporte2");
    Route::get('/servicios', function () {return view('servicios.servicios');})->name("servicios");
    Route::get('/habitaciones', [RoomController::class, 'index'])->name('habitaciones');
    Route::put('/habitaciones/{id}', [RoomController::class, 'updateRoom'])->name('updateRoom');
    Route::get('/habitaciones/buscar', [RoomController::class, 'buscar'])->name('habitaciones.buscar');
    Route::get('/configuracion', [InfoHotelController::class, 'index'])->name("configuracion");
    Route::put('/configuracion/{id}', [InfoHotelController::class, 'update'])->name('updateHotel');

});

Route::post('/logout', [WebController::class, 'logout'])->name('logout');

Route::get('/email', function () {return view('email');})->name("email");



Route::get('/csrf-token', function () {
    return csrf_token();
});

