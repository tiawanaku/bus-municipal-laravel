<?php

namespace App\Http\Controllers;

use App\Models\AsignacionDeBus;
use App\Models\SalidaDeBuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Parada;
use App\Models\Aviso;
use App\Models\Ruta;
use App\Models\Contenido;
use App\Models\Pasaje;
use App\Models\Horario;
use App\Models\Galeria;
use Carbon\Carbon;
use App\Models\Bus;
use App\Models\Designacion;
use App\Models\Salida;
use Illuminate\Support\Facades\Http;

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
        )
            ->with('ruta')
            ->get();

        $avisos = Aviso::where('status', 1)
            ->where('fin_periodo', '>', Carbon::now())
            ->get()
            ->map(function ($aviso) {
                $aviso->ubicacion = $aviso->ubicacion ? json_decode($aviso->ubicacion) : null;
                $aviso->created_at_humano = $aviso->created_at->diffForHumans();
                return $aviso;
            });

        $rutas = Ruta::all();

        return view('partials.index', [
            'avisos' => $avisos,
            'locations' => $paradas,
            'rutas' => $rutas
        ]);
    }


    /* Conexion con la api */
    function obtenerUbicacionesDeTodosLosDispositivos()
    {
        // 1. Obtener dispositivos desde la API externa
        $responseDevices = Http::withBasicAuth('milenkaelisaq95@gmail.com', '12345678')
            ->get('http://64.225.54.113:7541/api/devices/');

        if (!$responseDevices->ok()) {
            return response()->json(['error' => 'Error al obtener dispositivos'], 500);
        }

        $devices = $responseDevices->json();

        // 2. Obtener positionId válidos
        $positionIds = collect($devices)->pluck('positionId')->filter()->unique()->values()->all();

        if (empty($positionIds)) {
            return response()->json(['error' => 'No se encontraron positionId válidos'], 404);
        }

        // 3. Obtener posiciones
        $responsePositions = Http::withBasicAuth('milenkaelisaq95@gmail.com', '12345678')
            ->get('http://64.225.54.113:7541/api/positions', ['id' => $positionIds]);

        if (!$responsePositions->ok()) {
            return response()->json(['error' => 'Error al obtener posiciones'], 500);
        }

        $positions = collect($responsePositions->json());

        // 4. Obtener buses con relaciones necesarias
        $buses = Bus::with([
            'asignaciones.conductor',
            'asignaciones.anfitrion',
            'asignaciones.salidas.ruta', 
        ])->get()->keyBy('uniqueId');

        // 5. Armar resultado
        $resultado = collect($devices)->map(function ($device) use ($positions, $buses) {
            $position = $positions->firstWhere('id', $device['positionId']);
            $bus = $buses[$device['uniqueId']] ?? null;
            $numeroBus = $bus->numero_bus ?? 'sin número de bus';

            $rutaNombre = 'Ruta desconocida';
            $nombreConductor = 'Sin confirmar';
            $nombreAnfitrion = 'Sin confirmar';

            if ($bus && $bus->asignaciones->isNotEmpty()) {
                $hoy = now()->toDateString();
                $salida = null;
                $asignacionValida = null;

                foreach ($bus->asignaciones as $asignacion) {
                    $salidaActiva = $asignacion->salidas()
                        ->whereNull('fecha_llegada')
                        ->whereDate('fecha_salida', $hoy)
                        ->orderByDesc('id_salida_bus')
                        ->first();

                    if ($salidaActiva) {
                        $salida = $salidaActiva;
                        $asignacionValida = $asignacion;
                        break;
                    }
                }

                if ($salida) {
                    $rutaNombre = optional($salida->ruta)->nombre ?? 'Ruta desconocida';

                    if ($salida->conductor_confirmado) {
                        $nombreConductor = optional($asignacionValida->conductor)->nombre ?? 'Desconocido';
                    }

                    if ($salida->anfitrion_confirmado) {
                        $nombreAnfitrion = optional($asignacionValida->anfitrion)->nombre ?? 'Desconocido';
                    }
                }
            }

            return [
                'uniqueId' => $device['uniqueId'],
                'numero_bus' => $numeroBus,
                'ruta' => $rutaNombre,
                'conductor' => $nombreConductor,
                'anfitrion' => $nombreAnfitrion,
                'latitude' => $position['latitude'] ?? null,
                'longitude' => $position['longitude'] ?? null,
                'velocidad_kmh' => isset($position['speed']) ? round($position['speed'] * 3.6, 1) : null,
                'posicion' => $position,
            ];
        })// FILTRAMOS resultados con posición válida
            ->filter(fn($item) => $item['latitude'] && $item['longitude'])
            // FILTRAMOS los que tengan número de bus y ruta conocidos
            ->filter(fn($item) => $item['numero_bus'] !== 'sin número de bus' && $item['ruta'] !== 'Ruta desconocida')
            ->values();

        return response()->json($resultado);


    }
    /* Función para buscar las paradas por el buscador */
    public function buscar(Request $request)
    {
        $query = $request->input('query');

        $paradas = Parada::where('nombre_parada', 'like', '%' . $query . '%')
            ->get(['id_paradas', 'nombre_parada', 'sentido']);

        return response()->json($paradas);
    }
    /* Función para obtener la ubicación de la parada por el ID  */
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

    /* Función para traer todos los datos para la vista RUTA NORTE */
    public function showRutaNorte()
    {
        // Buscar la ruta que contenga "norte" (ignorando mayúsculas)
        $ruta = Ruta::whereRaw('LOWER(nombre) LIKE ?', ['%norte%'])->first();

        if (!$ruta) {
            abort(404, 'Ruta Norte no encontrada');
        }

        // Filtrar paradas con sentido 'ida' y ordenar por 'orden'
        $paradasIda = $ruta->paradas
            ->filter(function ($parada) {
                return strtolower($parada->sentido) === 'ida';
            })
            ->sortBy('orden');

        $paradasVuelta = $ruta->paradas
            ->filter(function ($parada) {
                return strtolower($parada->sentido) === 'vuelta';
            })
            ->sortBy('orden');

        return view('pageInformation.ruta-norte', [
            'paradasIda' => $paradasIda,
            'paradasVuelta' => $paradasVuelta,
            'rutas' => $ruta
        ]);
    }
    /* Función para traer todos los datos para la vista RUTA SUR */
    public function showRutaSur()
    {
        // Buscar la ruta que contenga "sur" (ignorando mayúsculas)
        $ruta = Ruta::whereRaw('LOWER(nombre) LIKE ?', ['%sur%'])->first();

        if (!$ruta) {
            abort(404, 'Ruta Sur no encontrada');
        }

        // Filtrar paradas con sentido 'ida' y ordenar por 'orden'
        $paradasIda = $ruta->paradas
            ->filter(function ($parada) {
                return strtolower($parada->sentido) === 'ida';
            })
            ->sortBy('orden');

        $paradasVuelta = $ruta->paradas
            ->filter(function ($parada) {
                return strtolower($parada->sentido) === 'vuelta';
            })
            ->sortBy('orden');

        return view('pageInformation.ruta-sur', [
            'paradasIda' => $paradasIda,
            'paradasVuelta' => $paradasVuelta,
            'rutas' => $ruta
        ]);
    }
    /* Función para traer todos los contenidos para la vista PRINCIPAL */
    public function paraPrincipal()
    {
        $header = Contenido::where('seccion', 'header')->first();
        $about = Contenido::where('seccion', 'about')->first();
        $mision = Contenido::where('seccion', 'mision')->first();
        $vision = Contenido::where('seccion', 'vision')->first();

        $pasajes = Pasaje::all();
        $horarios = Horario::all();
        $galerias = Galeria::where('activo', true)
            ->orderBy('created_at', 'desc') // más recientes primero
            ->get();

        return view('pageInformation.partials.principal', compact('header', 'about', 'mision', 'vision', 'pasajes', 'horarios', 'galerias'));
    }
}
