<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\GpsController;
use App\Http\Controllers\ParadaController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PdfController;
use Barryvdh\DomPDF\Facade\Pdf;



Route::get('/', function () {
    return view('partials.index');
});

Route::get('/', [InicioController::class, 'obtenerVista'], );
Route::get('/ubicacion', [InicioController::class, 'obtenerUbicacion']);

Route::get('/buscar', [InicioController::class, 'buscar']);
Route::get('/ubicacionparada', [InicioController::class, 'obtenerUbicacionParada']);

Route::get('/bus-municipal', [InicioController::class, 'paraPrincipal'])->name('bus-municipal');

Route::get('/ruta-norte', [InicioController::class, 'showRutaNorte'])->name('ruta-norte');

Route::get('/ruta-sur', [InicioController::class, 'showRutaSur'])->name('ruta-sur');


Route::get('/pdf/generate/mantenimientos', [PdfController::class, 'MantenimientosRecords'])->name('pdf.example');