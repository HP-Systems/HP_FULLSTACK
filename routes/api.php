<?php

use App\Events\nfcEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\DESKTOP\DeskController;
use App\Http\Controllers\DESKTOP\HuespedController;
use App\Http\Controllers\DESKTOP\PersonalController;
use App\Http\Controllers\DESKTOP\ReservasController as DesktopReservasController;
use App\Http\Controllers\DESKTOP\ServiciosController as DesktopServiciosController;
use App\Http\Controllers\DESKTOP\TarjetasController as DesktopTarjetasController;
use App\Http\Controllers\DESKTOP\LimpiezasController;
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
use App\Http\Controllers\GLOBAL\TarjetasController;
use App\Http\Controllers\WEB\dashboardController;

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
    Route::put('/reservas/delete/{idreserva}', [GlobalReservasController::class, 'cancelarReserva']);
    Route::post('/reservas/create', [GlobalReservasController::class, 'createReserva']);
    Route::get('/reservas/detalle/{idreserva}', [GlobalReservasController::class, 'detalleReserva']);

    Route::get('/services', [ServiciosController::class, 'index']);
    Route::post('/services/solicitar', [ServiciosController::class, 'insertarServiciosReserva']);
    Route::get('/services/historial/{idreserva}', [ServiciosController::class, 'obtenerServiciosReserva']);
    Route::post('/servicio/create', [ServiciosController::class, 'crearServicio']);
    Route::post('/tipoServicio/create', [ServiciosController::class, 'crearTipoServicio']);

    Route::post('/nfc/create',[TarjetasController::class, 'crearTarjeta']);
    Route::get('/nfc/index',[TarjetasController::class, 'indexTarjeta']);
    Route::post('/nfc/validate',[TarjetasController::class, 'validarTarjeta']);

});

//Version 1 movil
Route::prefix('v1/movil')->group(function () {
    Route::get('/inforamaionInicio', [HotelController::class, 'hotelIndex']);
    Route::get('/habitaciones', [HotelController::class, 'habitaciones']);
    Route::get('/tipoHabitaciones', [HotelController::class, 'tipoHabitaciones']);

    Route::get('/reservas/proceso/{iduser}', [ReservasController::class, 'obtenerReservasProceso']);
    Route::get('/reservas/futuras/{iduser}', [ReservasController::class, 'obtenerReservasFuturas']);
    Route::get('/reservas/historial/{iduser}', [ReservasController::class, 'obtenerReservasPasadasHuesped']);
    Route::post('/reservas/editHabitaciones/{idreserva}', [ReservasController::class, 'editarReservaHabitaciones']);
});

//version 1 Huespedes
Route::prefix('v1/huespedes')->group(function () {
    Route::post('/create', [DeskController::class, 'register']);
    Route::get('/index', [HuespedController::class, 'huespedes']);
    Route::put('/edit/{id}', [HuespedController::class, 'editar']);
});

//DESKTOP ROUTES
Route::prefix('desk')->group(function () {
    Route::post('/login', [DeskController::class, 'login']); 
    Route::post('/register', [DeskController::class, 'register']);
    Route::get('/users', [HuespedController::class, 'huespedes']);
    //Route::post('/editContra', [HuespedController::class, 'editarContra']);

    Route::put('/guestUpdate/{id}', [HuespedController::class, 'editar']);

    Route::get('/reservas', [DesktopReservasController::class, 'traerReservas']);
    Route::get('/reservas/checkin/{id}', [DesktopReservasController::class, 'checkIn']);
    Route::get('/reservas/checkout/{id}', [DesktopReservasController::class, 'checkOut']);
    Route::post('/reservas/create', [DesktopReservasController::class, 'createReserva']);

    Route::get('/tipoPersonal', [PersonalController::class, 'tipo_personal']);
    Route::get('/personal/index', [PersonalController::class, 'obtenerPersonal']);
    Route::post('/personal/create', [PersonalController::class, 'crearPersonal']);
    Route::put('/personal/edit/{id}', [PersonalController::class, 'editarPersonal']);
    Route::delete('/personal/delete/{id}', [PersonalController::class, 'desactivarPersonal']);

    Route::get('/tarjetas', [DesktopTarjetasController::class, 'traerTarjetas']);
    Route::post('/tarjetas/create', [DesktopTarjetasController::class, 'crearTarjeta']);
    Route::put('/tarjetas/edit/{id}', [DesktopTarjetasController::class, 'editTarjeta']);
    Route::post('/tarjetas/status/{id}', [DesktopTarjetasController::class, 'cambiarStatus']);
    Route::get('/tarjetas/tipos', [DesktopTarjetasController::class, 'obtenerTiposTarjetas']);
    Route::post('/tarjetas/asignarReserva/{id}', [DesktopTarjetasController::class, 'asignarTarjetaReserva']);

    Route::get('/reservas/{fecha1?}/{fecha2?}', [DesktopReservasController::class, 'traerReservas']);
    Route::get('/servicios/solicitados', [DesktopServiciosController::class, 'serviciosSolicitados']);
    Route::get('/servicios/completar/{id}', [DesktopServiciosController::class, 'completarServicio']);

    Route::get('/limpiezas/habitaciones', [LimpiezasController::class, 'habitacionesLimpieza']);

});

//WEB ROUTES
Route::prefix('web')->group(function () {
    Route::post('/login', [WebController::class, 'login']); 
    Route::post('/register', [WebController::class, 'register']);
    Route::get('/infoHotel', [InfoHotelController::class, 'infoHotel']);
    Route::put ('/updateHotel/{id}', [InfoHotelController::class, 'update']);

    Route::get ('/dashboard/servicios', [dashboardController::class, 'servicios']);
    Route::get ('dashboard/ventasMes',[dashboardController::class,'ventaPorMes']);
    Route::get ('dashboard/usuariosMes',[dashboardController::class,'usuariosPorMes']);
    Route::get('/dashboard/ingresosPorTipo', [dashboardController::class, 'ingresosPorTipoHabitacion']);
    
    /*Route::get('/rooms', [RoomController::class, 'index']);
    Route::post('/storeTipo', [RoomController::class, 'storeTipo']);
    Route::put('/updateTipo/{id}', [RoomController::class, 'updateTipo']);
    Route::post('/storeRoom', [RoomController::class, 'storeRoom']);
    Route::put('/updateRoom/{id}', [RoomController::class, 'updateRoom']);*/
    
});


Route::post('/users/insert', [UsersController::class, 'insertPersonal'])->name('personal.crear');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [DeskController::class, 'logout']);
});

Route::post('/prueba', function () {
    event(new nfcEvent('Hola mundo',2));
    return response()->json(['message' => 'Event has been sent!']);
});
