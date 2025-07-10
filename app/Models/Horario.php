<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Horario extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'turno',
        'descripcion',
        'desde',
        'hasta',
    ];

    protected $dates = [
        'deleted_at',
    ];
    /* Identifcar que turno es en base a la hora actual */
    public static function obtenerHorarioActual()
    {
        $ahora = now()->format('H:i:s');

        $horarios = self::all();

        foreach ($horarios as $horario) {
            $desde = $horario->desde;
            $hasta = $horario->hasta;

            if ($desde <= $hasta) {
                // Horario normal
                if ($ahora >= $desde && $ahora <= $hasta) {
                    return $horario;
                }
            } else {
                // Horario que cruza medianoche
                if ($ahora >= $desde || $ahora <= $hasta) {
                    return $horario;
                }
            }
        }

       
        return null;
    }

    public function getRangoHorarioAttribute()
    {
        return "{$this->turno} ({$this->desde} - {$this->hasta})";
    }
}
