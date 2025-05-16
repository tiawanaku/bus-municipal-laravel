<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioRecaudo extends Model
{
    use HasFactory;

    // Nombre de la tabla si no sigue la convención (opcional)
    protected $table = 'formulario_recaudo';

    // Atributos que pueden asignarse masivamente
    protected $fillable = [
        'anfitrion_id',
        'bus_id',
        'conductor_id',
        'ruta_id',
        'horario', // cámbialo si usas asignacion_id
        'cantidad_ventas_regulares',
        'rango_inicial_regulares',
        'rango_final_regulares',
        'monto_recaudado_regular',
        'total_recaudo_regular_preferencial',
        'cantidad_ventas_preferenciales',
        'rango_inicial_preferencial',
        'rango_final_preferencial',
        'monto_recaudado_preferencial',
        'fecha_recaudo',
        'observaciones',
        'estado',
    ];

    /**
     * Relación: Un formulario pertenece a una entrega de talonarios.
     */
   public function anfitrion()
{
    return $this->belongsTo(\App\Models\EntregaTalonariosAnfitrion::class, 'anfitrion_id');
}


    
    // Si en el futuro agregas relaciones con Bus, Conductor o Ruta:
    public function bus() { return $this->belongsTo(buses::class, 'bus_id'); }
    public function conductor() { return $this->belongsTo(conductors::class, 'conductor_id'); }
    public function ruta() { return $this->belongsTo(rutas::class, 'ruta_id'); }
}
