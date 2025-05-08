<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Aviso extends Model
{
    protected $fillable = [
        'noticia',
        'inicio_periodo',
        'fin_periodo',
        'razon',
        'paradas_afectadas',
        'detalle',
        'status',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::creating(function ($aviso) {
            // Set the user_id to the currently authenticated user's id
            $aviso->user_id = Auth::id();
        });
    }
}
