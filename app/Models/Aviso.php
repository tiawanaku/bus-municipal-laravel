<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Aviso extends Model
{
    // Campos que pueden asignarse masivamente (mass assignment)
    protected $fillable = [
        'noticia',
        'inicio_periodo',
        'fin_periodo',
        'razon',
        'paradas_afectadas',
        'ubicacion',
        'detalle',
        'status',
        'user_id',
    ];

    // Relación con el modelo User: un aviso pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Evento que se ejecuta cuando se crea un nuevo aviso
    protected static function booted()
    {
        static::creating(function ($aviso) {
            // Asigna automáticamente el id del usuario autenticado al crear un aviso
            $aviso->user_id = Auth::id();
        });
        
    }
}
