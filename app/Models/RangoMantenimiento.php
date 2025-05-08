<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RangoMantenimiento extends Model
{
    //
    protected $table = 'rango_mantenimientos';

    protected $fillable = ['km_min', 'km_max', 'tipo_mantenimiento'];
}
