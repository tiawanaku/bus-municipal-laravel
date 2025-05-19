<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaTalonariosAnfitrion extends Model
{
    use HasFactory;

    protected $table = 'entrega_talonarios_anfitrion';

    protected $fillable = [
        'anfitrion_id',
        'numero_autorizacion',
        'entrega_talonario_id',

        // Preferenciales
        'cantidad_talonarios_preferenciales',
        'rango_inicial_preferenciales',
        'rango_final_preferenciales',

        // Regulares
        'cantidad_talonarios_regulares',
        'rango_inicial_regulares',
        'rango_final_regulares',

        // Totales
        'total_tickets_regulares',
        'total_tickets_preferenciales',
        'total_recaudar_regulares',
        'total_recaudar_preferenciales',
        'total_recaudar',
    ];

    /**
     * Relación con el anfitrión
     */
    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class);
    }

    /**
     * Relación con el cajero que entrega
     */
    public function cajero()
    {
        return $this->belongsTo(Cajero::class);
    }
}