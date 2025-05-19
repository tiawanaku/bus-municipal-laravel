<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaTalonario extends Model
{
    use HasFactory;

    protected $table = 'entrega_talonarios';

   protected $fillable = [
    'cajero_id',
    'inventario_id',

    // Preferenciales
    'cantidad_preferenciales',
    'rango_inicial_preferencial',
    'rango_final_preferencial',
    'cantidad_restante_preferencial',
    'total_boletos_preferenciales',
    'total_aproximado_bolivianos_preferencial',

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
    'total_recaudacion_bolivianos',


    ];

    /**
     * Relación: Cajero secundario que recibe los talonarios
     */
    public function cajero()
    {
        return $this->belongsTo(Cajero::class, 'cajero_id');
    }

    /**
     * Relación: Cajero principal que entrega los talonarios
     */
    public function entregadoPor()
    {
        return $this->belongsTo(Cajero::class, 'entregado_por');
    }

    /**
     * Definir los tipos de datos de las relaciones
     */
    protected $casts = [
        'cajero_id' => 'integer',
        'entregado_por' => 'integer',
        'cantidad_preferenciales' => 'integer',
        'rango_inicial_preferencial' => 'integer',
        'rango_final_preferencial' => 'integer',
        'cantidad_restante_preferencial' => 'integer',
        'total_boletos_preferenciales' => 'integer',
        'total_aproximado_bolivianos' => 'float',

        'cantidad_regulares' => 'integer',
        'rango_inicial_regular' => 'integer',
        'rango_final_regular' => 'integer',
        'cantidad_restante_regular' => 'integer',
        'total_boletos_regulares' => 'integer',
        'total_aproximado_bolivianos_regular' => 'float',

        'estado_preferencial' => 'boolean',
        'estado_regular' => 'boolean',
    ];

    /**
     * Mutator para asegurar que el campo `fecha_entrega` siempre esté en el formato adecuado
     */
}