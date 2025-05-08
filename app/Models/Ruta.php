<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Ruta extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'recorrido'
    ];
    protected $casts = [
        'recorrido' => 'array',
    ];
    public function paradas()
    {
        return $this->hasMany(Parada::class, 'id_ruta');  
    }

   
}
