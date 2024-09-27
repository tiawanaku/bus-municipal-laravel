<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioTalonarios extends Model
{
    use HasFactory;

    protected $fillable = [
        'cajero_id',
        'codigo_autorizacion',
        'tipo_talonario',
        'cantidad_tickets',
        'rango_inicial',
        'rango_final',
        'numero_bloques',
        'valor_ticket_bs',
    ];

    // Relación con el modelo Cajero
    public function cajero()
    {
        return $this->belongsTo(Cajero::class, 'cajero_id');
    }
}