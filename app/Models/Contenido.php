<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contenido extends Model
{
    //
    use HasFactory, SoftDeletes;

     protected $table = 'contenidos';

    
    protected $primaryKey = 'id';

    
    protected $fillable = [
        'seccion',
        'titulo',
        'descripcion',
        'imagen'
    ];

}
