<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cajero extends Model
{
    use HasFactory;

    protected $table = 'cajeros';

    protected $fillable = [
        'tipo_cajero',
        'cajero_padre_id',
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

    // Relación con su cajero padre (si aplica)
    public function cajeroPadre()
    {
        return $this->belongsTo(Cajero::class, 'cajero_padre_id');
    }

    // Relación con los cajeros hijos (si es principal)
    public function cajerosHijos()
    {
        return $this->hasMany(Cajero::class, 'cajero_padre_id');
    }
}