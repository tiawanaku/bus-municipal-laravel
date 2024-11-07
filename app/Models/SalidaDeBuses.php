<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class SalidaDeBuses extends Model
{
    use HasFactory;

    // Nombre de la tabla si es diferente del plural del modelo
    protected $table = 'salida_de_buses';

    // Los campos que se pueden asignar en masa
    protected $fillable = [
        'id_designacion_bus',
        'n_bus',
        'n_ficha',
        'fecha_salida',
        'hora_salida',
        'id_anfitrion',
        'estado_salida',
        'motivo_no_salida',
    ];

    // Definición de la relación con el modelo Anfitrion
    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class, 'id_anfitrion');
    }

    // Si también deseas definir la relación con la designación de bus
    public function designacionBus()
    {
        return $this->belongsTo(AsignacionDeBus::class, 'id_designacion_bus');
    }
}
