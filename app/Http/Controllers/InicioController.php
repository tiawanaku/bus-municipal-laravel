<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Parada;
use App\Models\Aviso;
use App\Models\Ruta;

class InicioController extends Controller
{
    /* Función para obtener latitud y longitus de las paradas y obtener los avisos */
    public function obtenerVista()
    {

        $paradas = Parada::select(
            'nombre_parada',
            DB::raw("JSON_EXTRACT(lat_long, '$.lng') as longitud"),
            DB::raw("JSON_EXTRACT(lat_long, '$.lat') as latitud"),
            'sentido',
            'id_ruta',
            'orden',
            'id_paradas'
        )->get();


        $avisos = Aviso::where('status', 1)->get();
        foreach ($avisos as $aviso) {
            $aviso->created_at_humano = $aviso->created_at->diffForHumans();
        }
        $rutas = Ruta::all(); 
        

        return view('partials.index', [
            'avisos' => $avisos,
            'locations' => $paradas,
            'rutas' => $rutas
        ]);

    }

    /* Obtener la ubicación del bus a traves del GPS */
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
            ->get(['id_paradas', 'nombre_parada', 'sentido']);

        return response()->json($paradas);
    }
    /* Función para obtener la ubicación de la parada por el ID q */
    public function obtenerUbicacionParada(Request $request)
    {
        $id = $request->input('id_paradas');

        $parada = Parada::select(
            DB::raw("JSON_EXTRACT(lat_long, '$.lng') as longitud"),
            DB::raw("JSON_EXTRACT(lat_long, '$.lat') as latitud")
        )
            ->where('id_paradas', $id)
            ->first();

        if ($parada) {
            return response()->json($parada);
        } else {
            return response()->json(['error' => 'Parada no encontrada'], 404);
        }
    }
}
