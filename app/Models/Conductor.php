<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'tipo_licencia',
        'numero_contrato',
        'numero_contacto',
        'numero_referencia',
    ];

    // Método para obtener el nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}
