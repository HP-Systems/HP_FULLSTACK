<?php

use App\Http\Controllers\WEB\InfoHotelController;
use App\Http\Controllers\WEB\RoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\WebController;
use App\Http\Controllers\WEB\UsersController;
use App\Http\Controllers\WEB\ServiciosController;

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
    Route::get('/users/tipos', [UsersController::class, 'indexTipos'])->name('tipos_personal');
    Route::post('/users/insert', [UsersController::class, 'insertPersonal'])->name('insertPersonal');
    Route::post('/users/edit', [UsersController::class, 'editPersonal'])->name('editPersonal');
    Route::post('/users/status', [UsersController::class, 'cambiarStatus'])->name('cambiarStatus');
   
    Route::get('/reporte1', function () {return view('reportes.reporte1');})->name("reporte1");
    Route::get('/reporte2', function () {return view('reportes.reporte2');})->name("reporte2");

    Route::get('/servicios', [ServiciosController::class, 'index'])->name('servicios');
    Route::get('/servicios/tipos', function () {return view('servicios.tipos_servicios');})->name("tipos_servicios");
    Route::post('/servicios/insert', [ServiciosController::class, 'insertService'])->name('insertService');
    Route::post('/servicios/edit', [ServiciosController::class, 'editService'])->name('editService');
    Route::post('/servicios/status', [ServiciosController::class, 'cambiarStatus'])->name('cambiarStatusServicio');

    Route::get('/habitaciones', [RoomController::class, 'index'])->name('habitaciones');
    Route::put('/habitaciones/{id}', [RoomController::class, 'updateRoom'])->name('updateRoom');
    Route::get('/habitaciones/buscar', [RoomController::class, 'buscar'])->name('habitaciones.buscar');
    Route::get('/habitaciones/tipos', function () {return view('habitaciones.tipos_habitaciones');})->name("tipos_habitaciones"); 

    Route::get('/tarjetas', function () {return view('tarjetas.tarjetas');})->name("tarjetas");
    Route::get('/tarjetas/tipos', function () {return view('tarjetas.tipos_tarjetas');})->name("tipos_tarjetas");

    Route::get('/configuracion', [InfoHotelController::class, 'index'])->name("configuracion");
    Route::put('/configuracion/{id}', [InfoHotelController::class, 'update'])->name('updateHotel');

});

Route::post('/logout', [WebController::class, 'logout'])->name('logout');

Route::get('/email', function () {return view('email');})->name("email");



Route::get('/csrf-token', function () {
    return csrf_token();
});

