<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuplantacionConductor extends Model
{
    //
      use SoftDeletes;

    protected $table = 'suplantaciones_conductors';

    protected $fillable = [
        'salida_bus_id',
        'id_conductor_suplente',
        'motivo',
        'aprobado_por',
    ];

    // Relaciones con otras tablas (opcional)
    public function bus()
    {
        return $this->belongsTo(SalidaDeBuses::class, 'salida_bus_id');
    }

    public function conductorSuplente()
    {
        return $this->belongsTo(Anfitrion::class, 'id_conductor_suplente');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
