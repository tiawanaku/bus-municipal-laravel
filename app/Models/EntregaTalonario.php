<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InventarioTalonarios;

class EntregaTalonario extends Model
{
    use HasFactory;

    protected $table = 'entrega_talonarios';

    protected $fillable = [
        'responsable_entrega',
        'cajero_id',
        'users_id',
        'cantidad_preferenciales',
        'rango_inicial_preferencial',
        'rango_final_preferencial',
        'cantidad_restante_preferencial',
        'cantidad_regulares',
        'rango_inicial_regular',
        'rango_final_regular',
        'cantidad_restante_regular',
        'fecha_entrega',
    ];

    // Relación con el modelo Cajero
    public function cajero()
    {
        return $this->belongsTo(Cajero::class);
    }

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }





    // Sobrescribir el boot del modelo para ejecutar la lógica después de crear una entrega
    protected static function boot()
    {
        parent::boot();

        // Lógica después de crear una entrega de talonarios
        static::created(function ($entregaTalonario) {
            // Buscar el registro en InventarioTalonarios que coincida con el tipo de talonario
            $inventario = InventarioTalonarios::where('tipo_talonario', $entregaTalonario->tipo_talonarios)->first();

            if ($inventario) {
                // Verificar si hay suficientes paquetes en el inventario
                if ($inventario->numero_paquete >= $entregaTalonario->numero_paquetes_entregados) {
                    // Restar el número de paquetes entregados del inventario
                    $inventario->numero_paquete -= $entregaTalonario->numero_paquetes_entregados;

                    // Verificar si hay suficientes tickets en el inventario
                    if ($inventario->cantidad_tickets >= $entregaTalonario->cantidad_tickets) {
                        // Restar la cantidad de tickets entregados del inventario
                        $inventario->cantidad_tickets -= $entregaTalonario->cantidad_tickets;
                    } else {
                        // Lógica si no hay suficientes tickets
                        throw new \Exception('No hay suficientes tickets en el inventario para esta entrega.');
                    }

                    // Guardar el inventario actualizado
                    $inventario->save();
                } else {
                    // Lógica si no hay suficientes paquetes
                    throw new \Exception('No hay suficientes paquetes en el inventario para esta entrega.');
                }
            } else {
                throw new \Exception('No se encontró un registro de inventario para este tipo de talonario.');
            }
        });
    }
}