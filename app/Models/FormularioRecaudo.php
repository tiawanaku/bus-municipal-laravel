<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioRecaudo extends Model
{
    use HasFactory;

    protected $table = 'formulario_recaudo';

    protected $fillable = [
        'bus_id',
        'N_ficha',
        'conductor_id',
        'rutas',
        'horario',
        'cantidad_ventas_regulares',
        'rango_inicial_regulares',
        'rango_final_regulares',
        'monto_recaudado_regular',
        'cantidad_ventas_preferenciales',
        'rango_inicial_preferencial',
        'rango_final_preferencial',
        'monto_recaudado_preferencial',
        'total_recaudo_regular_preferencial',
    ];

    // Relaciones
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'conductor_id');
    }

    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class, 'anfitrion_id');
    }

    public function detallesRangos()
{
    return $this->hasMany(DetalleRecaudoRango::class);
}

}
