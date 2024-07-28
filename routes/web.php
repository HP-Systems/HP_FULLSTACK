<?php

use App\Http\Controllers\WEB\InfoHotelController;
use App\Http\Controllers\WEB\ReportesController;
use App\Http\Controllers\WEB\RoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\WebController;
use App\Http\Controllers\WEB\UsersController;
use App\Http\Controllers\WEB\ServiciosController;
use App\Http\Controllers\WEB\TarjetasController;
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
Route::post('/logout', [WebController::class, 'logout'])->name('logout');

Route::get('/email', function () {return view('email');})->name("email");
Route::get('/verify', function () {return view('login.verify');})->middleware('signed')->name("verify");
Route::post('/verifyNumber', [WebController::class, 'verifyNumber']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', function () {return view('home');})->name("home");
    Route::get('/dashboard', function () {return view('dashboard.dashboard');})->name("dashboard");
    
    //PERSONAL
    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::post('/users/insert', [UsersController::class, 'insertPersonal'])->name('insertPersonal');
    Route::post('/users/edit', [UsersController::class, 'editPersonal'])->name('editPersonal');
    Route::post('/users/status', [UsersController::class, 'cambiarStatus'])->name('cambiarStatus');

    //TIPOS DE PERSONAL
    Route::get('/users/tipos', [UsersController::class, 'indexTipos'])->name('tipos_personal');
    Route::post('/users/tipos/insert', [UsersController::class, 'insertTipoPersonal'])->name('insertTipoPersonal');
    Route::post('/users/tipos/edit', [UsersController::class, 'editTipoPersonal'])->name('editTipoPersonal');
    Route::post('/users/tipos/status', [UsersController::class, 'cambiarStatusTipo'])->name('cambiarStatusTipoPersonal');
   
    //REPORTES
    Route::get('/reporte1', [ReportesController::class,'ventasPorMes'])->name("reporte1");
    Route::post('/reporte1', [ReportesController::class,'ventasPorMesFiltrar'])->name("reporte1.filtar");
    Route::get('/reporte1/pdf', [ReportesController::class,'pdfVentasPorMes'])->name("reporte1.pdf");


    //SERVICIOS
    Route::get('/servicios', [ServiciosController::class, 'index'])->name('servicios'); 
    Route::post('/servicios/insert', [ServiciosController::class, 'insertService'])->name('insertService');
    Route::post('/servicios/edit', [ServiciosController::class, 'editService'])->name('editService');
    Route::post('/servicios/status', [ServiciosController::class, 'cambiarStatus'])->name('cambiarStatusServicio');

    //TIPOS DE SERVICIOS
    Route::get('/servicios/tipos', [ServiciosController::class, 'indexTipos'])->name('tipos_servicios');
    Route::post('/servicios/tipos/insert', [ServiciosController::class, 'insertTipoServicio'])->name('insertTipoServicio');
    Route::post('/servicios/tipos/edit', [ServiciosController::class, 'editTipoServicio'])->name('editTipoServicio');
    Route::post('/servicios/tipos/status', [ServiciosController::class, 'cambiarStatusTipo'])->name('cambiarStatusTipoServicio');

    //HABITACIONES
    Route::get('/habitaciones', [RoomController::class, 'index'])->name('habitaciones');
    Route::put('/habitaciones/{id}', [RoomController::class, 'updateRoom'])->name('updateRoom');
    Route::get('/habitaciones/buscar', [RoomController::class, 'buscar'])->name('habitaciones.buscar');
   
    //TIPOS DE HABITACIONES
    Route::get('/habitaciones/tipos', [RoomController::class, 'indexTipos'])->name('tipos_habitaciones');
    Route::post('/habitaciones/tipos/insert', [RoomController::class, 'insertTipoHabitacion'])->name('insertTipoHabitacion');
    Route::post('/habitaciones/tipos/edit', [RoomController::class, 'editTipoHabitacion'])->name('editTipoHabitacion');
    Route::post('/habitaciones/tipos/status', [RoomController::class, 'cambiarStatusTipo'])->name('cambiarStatusTipoHabitacion');

    //TARJETAS
    Route::get('/tarjetas', function () {return view('tarjetas.tarjetas');})->name("tarjetas");

    //TIPOS DE TARJETAS
    Route::get('/tarjetas/tipos', [TarjetasController::class, 'indexTipos'])->name('tipos_tarjetas');
    Route::post('/tarjetas/tipos/insert', [TarjetasController::class, 'insertTipoTarjeta'])->name('insertTipoTarjeta');
    Route::post('/tarjetas/tipos/edit', [TarjetasController::class, 'editTipoTarjeta'])->name('editTipoTarjeta');
    Route::post('/tarjetas/tipos/status', [TarjetasController::class, 'cambiarStatusTipo'])->name('cambiarStatusTipoTarjeta');

    //HABITACIONES
    Route::post('/habitaciones/store', [RoomController::class, 'storeRoom'])->name('room.store');
    
    //CONFIGURACION
    Route::get('/configuracion', [InfoHotelController::class, 'index'])->name("configuracion");
    Route::put('/configuracion/{id}', [InfoHotelController::class, 'update'])->name('updateHotel');

});


Route::get('/csrf-token', function () {
    return csrf_token();
});

