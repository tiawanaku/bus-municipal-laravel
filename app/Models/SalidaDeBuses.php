<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class SalidaDeBuses extends Model
{
    use HasFactory;

    // Nombre de la tabla si es diferente del plural del modelo
    protected $table = 'salida_de_buses';

    protected $primaryKey = 'id_salida_bus';

    // Los campos que se pueden asignar en masa
    protected $fillable = [
        'designacion_id',
        'fecha_salida',
        'hora_salida',
        'estado_salida',
        'motivo_no_salida',
        'kilometraje_salida',
        'kilometraje_llegada',
        'tipo_mantenimiento',
        'status_mantenimiento',
        'conductor_confirmado',
        'anfitrion_confirmado',





    ];



    

    // Si también deseas definir la relación con la designación de bus
    public function designacionBus()
    {
        return $this->belongsTo(AsignacionDeBus::class, 'designacion_id');
    }
}
