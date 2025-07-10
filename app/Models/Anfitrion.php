<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

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
    /* Para auditoria */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['nombre', 'apellido_paterno','apellido_maternno','ci',
        'complemento',
        'ci_expedido',
        'celular',
        'genero',
        'numero_contrato',
        'fecha_inicio_contrato',
        'fecha_fin_contrato',]);
    }

    // Método para obtener el nombre completo del anfitrión
    public function getFullNameAttribute()
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }
    /* Relación con Asignación de Buses */
    public function asignaciones()
    {
        return $this->hasMany(AsignacionDeBus::class, 'id_anfitrion');
    }

}