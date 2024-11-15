<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SeguimientoController extends Controller


{
    
    public function enviarTexto()
    {
        // El texto que quieres enviar a la vista
        $texto = "¡Este es el texto enviado desde el controlador!";

        // Retornar la vista con los datos
        return view('filament.pages.seguimiento', compact('texto'));
    }
}
