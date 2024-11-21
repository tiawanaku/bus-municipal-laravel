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
    return view('pageInformation.app'); 
})->name('bus-municipal');

Route::get('/ruta-norte', function () {
    return view('pageInformation.ruta-norte');
})->name('ruta-norte');

Route::get('/ruta-sur', function () {
    return view('pageInformation.ruta-sur');
})->name('ruta-sur');

