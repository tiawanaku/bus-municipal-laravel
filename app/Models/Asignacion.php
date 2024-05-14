<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'conductor_id',
        'anfitrion_id',
        'ruta_id',
        'horario',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function conductor()
    {
        return $this->belongsTo(Conductor::class);
    }

    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class);
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }
}
