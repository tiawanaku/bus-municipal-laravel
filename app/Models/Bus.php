<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero_placa',
        'numero_bus',
        'numero_chasis',
        ];

       
        
        public function mantenimientos() {
            return $this->hasMany(Mantenimiento::class);
        }
}
