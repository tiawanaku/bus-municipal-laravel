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

   
}
