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
        'preferencial_del',
        'preferencial_al',
        'cantidad_preferenciales',
        'rango_inicial_preferencial',
        'rango_final_preferencial',
        'cantidad_restante_preferencial',
        'total_boletos_preferenciales',
        'total_aproximado_bolivianos_preferencial',

        // Regulares
        'regular_del',
        'regular_al',
        'cantidad_regulares',
        'rango_inicial_regular',
        'rango_final_regular',
        'cantidad_restante_regular',
        'total_boletos_regulares',
        'total_aproximado_bolivianos_regular',

        // Adicional
        'estado_preferencial',
        'estado_regular',
        'tipo_talonario',
        'fecha_entrega',
        'observaciones',
        'total_recaudacion_bolivianos',
    ];

    /**
     * Boot del modelo para auto-calcular valores
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-calcular preferencial_del si está vacío
            if (empty($model->preferencial_del) && !empty($model->preferencial_al)) {
                $ultimo = self::whereNotNull('preferencial_al')
                    ->where('preferencial_al', '>', 0)
                    ->orderByDesc('id')
                    ->first();
                
                $model->preferencial_del = $ultimo ? $ultimo->preferencial_al + 1 : 1;
            }

            // Auto-calcular regular_del si está vacío
            if (empty($model->regular_del) && !empty($model->regular_al)) {
                $ultimo = self::whereNotNull('regular_al')
                    ->where('regular_al', '>', 0)
                    ->orderByDesc('id')
                    ->first();
                
                $model->regular_del = $ultimo ? $ultimo->regular_al + 1 : 1;
            }
        });
    }

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
}