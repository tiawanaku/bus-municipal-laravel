<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ControlDeRegulador extends Model
{
   use HasFactory;

    protected $table = 'control_de_reguladors';

    protected $fillable = [
        'molinete_inicial',
        'foto_respaldo_inicial',
        'molinete_final',
        'foto_respaldo_final',
        'total_giros',
        'fecha_envio',
        'user_id',
        'bus_id', // Campo anticipado para relación con Bus
    ];

    /**
     * Relación: el regulador fue reportado por un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: el regulador está asociado a un bus.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
