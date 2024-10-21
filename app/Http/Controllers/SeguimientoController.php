<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SeguimientoController extends Controller


{
    
    public function obtenerSeguimiento(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|string',
            'start_day' => 'required|date',
            'end_day' => 'required|date|after_or_equal:start_day',
        ]);

        $response = Http::get("http://127.0.0.1:8001/tracking-data", [
            'start_day' => $validated['start_day'],
            'end_day' => $validated['end_day'],
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'No se pudo obtener los datos'], 500);
    }
}
