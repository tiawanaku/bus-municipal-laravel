<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galeria extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'imagen',
        'descripcion',
        'activo',
    ];
}
