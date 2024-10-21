<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Parada;

class GPSController extends Controller
{
    /* Función para obtener latitud y longitus de las paradas */
    public function obtenerVista()
    {
        
        $paradas = Parada::select('nombre_parada', DB::raw('ST_X(lat_long) AS longitud'), DB::raw('ST_Y(lat_long) AS latitud'))->get();

       
        error_log(print_r($paradas, true));

        
        
        return view('layouts.app', ['locations' => $paradas]);
        /* return view ('layouts.app'); */
    }

    /* Obtener la ubicación del bus a traves del Bus */
    public function obtenerUbicacion()
    {
        $url = 'http://64.225.54.113:8047/bus20'; 

        
        $ch = curl_init($url);

       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Para que cURL devuelva el resultado como una cadena
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            
        ]);

        
        $response = curl_exec($ch);

        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => "Error al obtener ubicación: $error"], 500);
        }

        
        curl_close($ch);

        
        $data = json_decode($response, true);

        
        return response()->json($data);

    }

     /* Función para buscar las paradas por el buscador */
     public function buscar(Request $request)
     {
         $query = $request->input('query');
         
         $paradas = Parada::where('nombre_parada', 'like', '%' . $query . '%')
         ->get(['id_paradas', 'nombre_parada']); 
 
         return response()->json($paradas); 
     }
 /* Función para obtener la ubicación de la parada por el ID q */
     public function obtenerUbicacionParada(Request $request)
     {
         $id = $request->input('id_paradas'); 
 
         $parada = Parada::select(
                             DB::raw('ST_X(lat_long) AS longitud'), 
                             DB::raw('ST_Y(lat_long) AS latitud'))
                         ->where('id_paradas', $id)
                         ->first();
 
         if ($parada) {
             return response()->json($parada);
         } else {
             return response()->json(['error' => 'Parada no encontrada'], 404);
         }
     }
}
