<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class EntregaTalonariosAnfitrion extends Model
{
    use HasFactory;

    protected $table = 'entrega_talonarios_anfitrion';

    protected $fillable = [
        'anfitrion_id',
        'cajero_id',
        'numero_autorizacion',
        'cantidad_talonarios_preferenciales',
        'rango_inicial_preferenciales',
        'rango_final_preferenciales',
        'cantidad_talonarios_regulares',
        'rango_inicial_regulares',
        'rango_final_regulares',
        'total_tickets_regulares',
        'total_tickets_preferenciales',
        'total_recaudar_regulares',
        'total_recaudar_preferenciales',
        'total_recaudar',
    ];

    // Relaciones
    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class, 'anfitrion_id');
    }

    public function cajero()
    {
        return $this->belongsTo(Cajero::class, 'cajero_id');
    }
}