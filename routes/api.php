<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\DESKTOP\DeskController;
use App\Http\Controllers\DESKTOP\HuespedController;
use App\Http\Controllers\DESKTOP\ReservasController as DesktopReservasController;
use App\Http\Controllers\MOVIL\HotelController;
use App\Http\Controllers\MOVIL\MovilController;
use App\Http\Controllers\MOVIL\ReservasController;
use App\Http\Controllers\WEB\InfoHotelController;
use App\Http\Controllers\WEB\RoomController;
use App\Http\Controllers\WEB\WebController;
use App\Http\Controllers\WEB\UsersController;
use App\Http\Controllers\GLOBAL\HabitacionesController;
use App\Http\Controllers\GLOBAL\ServiciosController;
use App\Http\Controllers\GLOBAL\ReservasController as GlobalReservasController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//MOVIL ROUTES 
Route::prefix('movil')->group(function () {
    Route::post('/register', [MovilController::class, 'register']); 
    Route::post('/login', [MovilController::class, 'login']); 
});

//Version 1 global
Route::prefix('v1')->group(function () {
    Route::post('/habitaciones/disponibles', [HabitacionesController::class, 'habitacionesDisponibles']);
    Route::delete('/reservas/delete/{idreserva}', [GlobalReservasController::class, 'cancelarReserva']);

    Route::get('/services', [ServiciosController::class, 'index']);
    Route::post('/tipoServicio/create', [ServiciosController::class, 'crearTipoServicio']);
    Route::post('/servicio/create', [ServiciosController::class, 'crearServicio']);
});

//Version 1 movil
Route::prefix('v1/movil')->group(function () {
    Route::get('/inforamaionInicio', [HotelController::class, 'hotelIndex']);
    Route::get('/habitaciones', [HotelController::class, 'habitaciones']);
    Route::get('/tipoHabitaciones', [HotelController::class, 'tipoHabitaciones']);

    Route::post('/reservas/create', [ReservasController::class, 'createReserva']);
    Route::get('/reservas/{iduser}', [ReservasController::class, 'obtenerReservasHuesped']);
    Route::get('/reservas/historial/{iduser}', [ReservasController::class, 'obtenerReservasPasadasHuesped']);
    Route::put('/reservas/editHabitaciones/{idreserva}', [ReservasController::class, 'editarReservaHabitaciones']);
});

//DESKTOP ROUTES
Route::prefix('desk')->group(function () {
    Route::post('/login', [DeskController::class, 'login']); 
    Route::post('/register', [DeskController::class, 'register']);
    Route::get('/users', [HuespedController::class, 'huespedes']);
    Route::get('/reservas', [DesktopReservasController::class, 'traerReservas']);
    Route::post('/reservas/create', [DesktopReservasController::class, 'createReserva']);
});

//WEB ROUTES
Route::prefix('web')->group(function () {
    Route::post('/login', [WebController::class, 'login']); 
    Route::post('/register', [WebController::class, 'register']);
    Route::get('/infoHotel', [InfoHotelController::class, 'infoHotel']);
    Route::put ('/updateHotel/{id}', [InfoHotelController::class, 'update']);
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::post('/storeTipo', [RoomController::class, 'storeTipo']);
    Route::put('/updateTipo/{id}', [RoomController::class, 'updateTipo']);
    Route::post('/storeRoom', [RoomController::class, 'storeRoom']);
    Route::put('/updateRoom/{id}', [RoomController::class, 'updateRoom']);
});


Route::post('/users/insert', [UsersController::class, 'insertPersonal'])->name('personal.crear');

