<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InventarioTalonarios extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'cajero_id',
        'codigo_autorizacion',
        'tipo_talonario',
        'cantidad_tickets',
        'numero_paquete',
        'talonarios_por_paquete',
        'rango_inicial',
        'rango_final',
        'valor_ticket_bs',
        'entregado_at',
        'cajero_actual_id',
    ];
    
    protected $casts = [
        'entregado_at' => 'datetime',
    ];
    
    // Relación con el cajero responsable del ingreso al inventario
    public function cajero(): BelongsTo
    {
        return $this->belongsTo(Cajero::class);
    }
    
    // Relación con el cajero actual que tiene asignado el talonario
    public function cajeroActual(): BelongsTo
    {
        return $this->belongsTo(Cajero::class, 'cajero_actual_id');
    }
    
    // Relación con las entregas de talonarios
    public function entregaTalonarios(): BelongsToMany
    {
        return $this->belongsToMany(EntregaTalonario::class, 'entrega_talonario_inventario')
            ->withTimestamps();
    }
    
    // Scope para talonarios disponibles (no entregados)
    public function scopeDisponibles($query)
    {
        return $query->whereNull('entregado_at');
    }
    
    // Scope para talonarios entregados
    public function scopeEntregados($query)
    {
        return $query->whereNotNull('entregado_at');
    }
}