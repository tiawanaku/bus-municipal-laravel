<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EntregaTalonario extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'responsable_entrega',
        'cajero_id',
        'numero_paquetes_entregados',
        'cantidad_talonarios',
        'cantidad_tickets',
        'fecha_entrega',
        'tipo_talonarios',
        'observaciones',
        'inventario_talonarios_ids', // Campo para gestionar los IDs seleccionados
    ];
    
    protected $casts = [
        'fecha_entrega' => 'date',
        'inventario_talonarios_ids' => 'array',
    ];
    
    // Relación con el cajero
    public function cajero(): BelongsTo
    {
        return $this->belongsTo(Cajero::class);
    }
    
    // Relación con los talonarios de inventario asignados
    public function inventarioTalonarios(): BelongsToMany
    {
        return $this->belongsToMany(InventarioTalonarios::class, 'entrega_talonario_inventario')
            ->withTimestamps();
    }
    
    // Método para ejecutar después de crear/actualizar la entrega
    protected static function booted()
    {
        static::saved(function ($entregaTalonario) {
            // Si tenemos IDs de talonarios, los asociamos a esta entrega
            if (!empty($entregaTalonario->inventario_talonarios_ids)) {
                // Primero sincronizamos las relaciones
                $entregaTalonario->inventarioTalonarios()->sync($entregaTalonario->inventario_talonarios_ids);
                
                // Luego marcamos los talonarios como entregados
                InventarioTalonarios::whereIn('id', $entregaTalonario->inventario_talonarios_ids)
                    ->update([
                        'entregado_at' => now(),
                        'cajero_actual_id' => $entregaTalonario->cajero_id,
                    ]);
            }
        });
    }
}