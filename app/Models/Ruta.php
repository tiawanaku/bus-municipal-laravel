<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Parada;

class Ruta extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
    ];

    public function paradas()
    {
        return $this->hasMany(Parada::class, 'id_ruta');
    }

    protected static function booted()
    {
        static::saved(function ($ruta) {
            // Si se enviaron paradas seleccionadas en la solicitud, las actualizamos
            if (request()->has('selected_paradas')) {
                // Primero, desvincula las paradas que ya no pertenecen a esta ruta
                Parada::where('id_ruta', $ruta->id)->update(['id_ruta' => null]);

                // Luego, asigna las nuevas paradas seleccionadas a la ruta
                Parada::whereIn('id_paradas', request('selected_paradas'))
                    ->update(['id_ruta' => $ruta->id]);
            }
        });
    }
}
