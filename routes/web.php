<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntregaController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/entregas/create', [EntregaController::class, 'create'])->name('entregas.create');
Route::post('/entregas', [EntregaController::class, 'store'])->name('entregas.store');