<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaTalonariosAnfitrion extends Model
{
    use HasFactory;

    protected $table = 'entrega_talonarios_anfitrion';

    protected $fillable = [
        'entrega_talonario_id',
        'anfitrion_id',
        'preferencial_Del',
        'preferencial_Al',
        'regular_Del',
        'regular_Al',
        'cantidad_preferenciales',
        'rango_inicial_preferencial',
        'rango_final_preferencial',
        'total_boletos_preferenciales',
        'total_aproximado_bolivianos_preferencial',
        'cantidad_restante_preferencial',
        'cantidad_regulares',
        'rango_inicial_regular',
        'rango_final_regular',
        'total_boletos_regulares',
        'total_aproximado_bolivianos_regular',
        'cantidad_restante_regular',
        'estado_preferencial',
        'estado_regular',
        'fecha_entrega',
        'observaciones',
        'total_recaudacion_bolivianos',
        'estado',
        'tipo_talonarios'
    ];

    // ✅ Relación con EntregaTalonario
    public function entregaTalonario()
    {
        return $this->belongsTo(EntregaTalonario::class, 'entrega_talonario_id');
    }

    // ✅ Relación con Anfitrion
    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class, 'anfitrion_id');
    }

    // ✅ Acceso al cajero a través de entregaTalonario
    public function getCajeroAttribute()
    {
        return $this->entregaTalonario?->cajero;
    }
}