<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Ruta;

class Parada extends Model
{
    use HasFactory;
    
    protected $table = 'paradas';

    
    protected $primaryKey = 'id_paradas';

    
    protected $fillable = [
        'nombre_parada',
        'sentido',
        'lat_long',
        'id_ruta',
    ];

    
    public function ruta()
    {
        /* return $this->belongsTo(Ruta::class, 'id_ruta'); */
        return $this->belongsTo(Ruta::class, 'id_ruta');
    }

  
}
