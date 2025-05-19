<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anfitrion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'ci',
        'complemento',
        'ci_expedido',
        'celular',
        'genero',
        'numero_contrato',
        'fecha_inicio_contrato',
        'fecha_fin_contrato',
    ];

    protected $casts = [
        'fecha_inicio_contrato' => 'date',
        'fecha_fin_contrato' => 'date',
    ];

    // Método para obtener el nombre completo del anfitrión
    public function getFullNameAttribute()
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }
}