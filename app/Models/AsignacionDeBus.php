<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'fin_asignacion',
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
    
}