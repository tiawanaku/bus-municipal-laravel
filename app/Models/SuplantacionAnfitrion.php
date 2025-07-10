<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuplantacionAnfitrion extends Model
{
     use SoftDeletes;

    protected $table = 'suplantaciones_anfitrions';

    protected $fillable = [
        'salida_bus_id',
        'id_anfitrion_suplente',
        'motivo',
        'aprobado_por',
    ];

    // Relaciones con otras tablas (opcional)
    public function bus()
    {
        return $this->belongsTo(SalidaDeBuses::class, 'salida_bus_id');
    }

    public function anfitrionSuplente()
    {
        return $this->belongsTo(Anfitrion::class, 'id_anfitrion_suplente');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
   
}
