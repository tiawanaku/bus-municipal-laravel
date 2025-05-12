<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioTalonarios extends Model
{
    use HasFactory;

    protected $fillable = [
        'cajero_id',
        'cantidad_preferenciales',
        'rango_inicial_preferencial',
        'rango_final_preferencial',
        'cantidad_restante_preferencial',
        'cantidad_regulares',
        'rango_inicial_regular',
        'rango_final_regular',
        'cantidad_restante_regular',
        'tipo_talonarios',
        'fecha_entrega',
        'observaciones',
    ];

    // Relación con el modelo Cajero
    public function cajero()
    {
        return $this->belongsTo(Cajero::class, 'cajero_id');
    }
}
