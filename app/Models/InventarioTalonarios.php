<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioTalonarios extends Model
{
    use HasFactory;

    protected $table = 'inventario_talonarios';

    protected $fillable = [
        'cajero_id',

        // Preferenciales
        'cantidad_preferenciales',
        'rango_inicial_preferencial',
        'rango_final_preferencial',
        'cantidad_restante_preferencial',
        'total_boletos_preferenciales',
        'total_aproximado_bolivianos',

        // Regulares
        'cantidad_regulares',
        'rango_inicial_regular',
        'rango_final_regular',
        'cantidad_restante_regular',
        'total_boletos_regulares',
        'total_aproximado_bolivianos_regular',

        // Adicional
        'estado_preferencial',
        'estado_regular',
        'tipo_talonarios',
        'fecha_entrega',
        'observaciones',
    ];

    /**
     * Relación: InventarioTalonario pertenece a un Cajero
     */
    public function cajero()
    {
        return $this->belongsTo(Cajero::class);
    }
}
