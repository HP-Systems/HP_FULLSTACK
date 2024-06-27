<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\DESKTOP\DeskController;
use App\Http\Controllers\MOVIL\MovilController;
use App\Http\Controllers\WEB\WebController;
use App\Http\Controllers\WEB\UsersController;

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

//DESKTOP ROUTES
Route::prefix('desk')->group(function () {
    Route::post('/login', [DeskController::class, 'login']); 
    Route::post('/register', [DeskController::class, 'register']);
});

//WEB ROUTES
Route::prefix('web')->group(function () {
    Route::post('/login', [WebController::class, 'login']); 
    Route::post('/register', [WebController::class, 'register']);
});


Route::post('/users/insert', [UsersController::class, 'insertPersonal'])->name('personal.crear');

