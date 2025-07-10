<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AsignacionDeBus extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Nombre de la tabla
    protected $table = 'asignacion_de_bus';

    // Clave primaria
    protected $primaryKey = 'id_designacion_bus';

    // Campos asignables
    protected $fillable = [
        'id_conductor',
        'id_buses',
        'id_anfitrion',
        'n_bus',
        'observaciones',
        'fecha_designacion',
        'fin_designacion',
        'tipo_asignacion',
        'n_ficha',
        'hora_salida'
    ];

    /**
     * Relación con el modelo Conductor.
     */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'id_conductor');
    }

    /**
     * Relación con el modelo Bus.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'id_buses');
    }
    /**
     * Relación con el modelo de anfitrion.
     */
    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class, 'id_anfitrion');
    }

    /* Funcion para verificar su disponibilidad */
    public static function buscarDisponibilidad($fechaInicio, $fechaFin)
    {
        //  Buscar anfitriones NO asignados en el rango de fechas
        $anfitrionesDisponibles = Anfitrion::whereDoesntHave('asignaciones', function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_designacion', [$fechaInicio, $fechaFin])
                ->orWhereBetween('fin_designacion', [$fechaInicio, $fechaFin]);
        })->pluck(DB::raw("CONCAT(nombre, ' ', COALESCE(apellido_paterno, ''), ' ', COALESCE(apellido_materno, ''))"), 'id');

        //  Buscar conductores NO asignados en el rango de fechas
        $conductoresDisponibles = Conductor::whereDoesntHave('asignaciones', function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_designacion', [$fechaInicio, $fechaFin])
                ->orWhereBetween('fin_designacion', [$fechaInicio, $fechaFin]);
        })->pluck(DB::raw("CONCAT(nombre, ' ', COALESCE(apellido_paterno, ''), ' ', COALESCE(apellido_materno, ''))"), 'id');

        //  Buscar buses NO asignados en el rango de fechas
        $busesDisponibles = Bus::whereDoesntHave('asignaciones', function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_designacion', [$fechaInicio, $fechaFin])
                ->orWhereBetween('fin_designacion', [$fechaInicio, $fechaFin]);
        })->pluck('numero_bus', 'id');



        return [
            'anfitriones' => $anfitrionesDisponibles,
            'conductores' => $conductoresDisponibles,
            'buses' => $busesDisponibles,
        ];
    }
    /* Relación con salida de buses */
    public function salidas()
{
    return $this->hasMany(SalidaDeBuses::class, 'designacion_id');
}
 /* Ulitma salida registrada */
    public function ultimaSalidaActiva()
    {
       return $this->hasOne(SalidaDeBuses::class, 'designacion_id')
        ->whereNull('fecha_llegada')
        ->orderByDesc('id_salida_bus'); 
    }
}