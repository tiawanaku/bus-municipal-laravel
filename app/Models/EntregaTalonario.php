<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InventarioTalonarios;

class EntregaTalonario extends Model
{
    use HasFactory;

    protected $table = 'entrega_talonarios';

    protected $fillable = [

        'cajero_id',
        'users_id',
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
    ];

    // Relación con el modelo Cajero
    public function cajero()
    {
        return $this->belongsTo(Cajero::class);
    }

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
