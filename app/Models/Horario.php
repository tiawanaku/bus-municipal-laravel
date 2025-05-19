<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'turno',
        'descripcion',
        'desde',
        'hasta',
    ];

    protected $dates = [
        'deleted_at',
    ];
}
