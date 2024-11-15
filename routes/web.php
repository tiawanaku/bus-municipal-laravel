<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\GpsController;
use App\Http\Controllers\SeguimientoController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [GPSController::class, 'obtenerVista'],);
Route::get('/ubicacion', [GpsController::class, 'obtenerUbicacion']);

Route::get('/buscar', [GPSController::class, 'buscar']);
Route::get('/ubicacionparada', [GPSController::class, 'obtenerUbicacionParada']);


Route::get('/bus-municipal', function () {
    return view('page-information.app'); // Cambia 'bus-municipal' por el nombre de tu vista.
})->name('bus-municipal');

