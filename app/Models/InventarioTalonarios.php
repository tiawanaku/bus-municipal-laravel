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
        'tipo_talonarios',
        'fecha_entrega',
        'observaciones',

        // Citas y PDF
        'cite_nota_solicitud',
        'cite_nota_solicitud_filename',
        'n_cite',
        'gestion',
        'n_dosificacion',
        'numero_autorizacion',
        'fecha_solicitud_dosificacion',
        'fecha_autorizacion', // corregir en DB si hay un error tipográfico
        'fecha_activacion',

        // Total general
        'total_recaudacion_bolivianos',
    ];
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    /**
     * Relación: InventarioTalonario pertenece a un Cajero
     */
    public function cajero()
    {
        return $this->belongsTo(Cajero::class, 'cajero_id');
    }
}