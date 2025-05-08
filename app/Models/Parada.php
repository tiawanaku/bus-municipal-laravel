<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\Ruta;


class Parada extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $table = 'paradas';

    
    protected $primaryKey = 'id_paradas';

    
    protected $fillable = [
        'id_paradas',
        'nombre_parada',
        'sentido',
        'lat_long',
        'id_ruta',
        'orden',
    ];
   
    
    public function ruta()
    {
        
        return $this->belongsTo(Ruta::class, 'id_ruta'); 
    }
    
    
}
