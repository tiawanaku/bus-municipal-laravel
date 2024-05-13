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
    'fecha_nacimiento',
    'numero_contrato',
    'numero_contacto',
    'numero_referencia',
    ];
}
