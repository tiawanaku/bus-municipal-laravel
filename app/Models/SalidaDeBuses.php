<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Notifications\Notification;



class SalidaDeBuses extends Model
{
    use HasFactory;


    // Nombre de la tabla si es diferente del plural del modelo
    protected $table = 'salida_de_buses';

    protected $primaryKey = 'id_salida_bus';

    // Los campos que se pueden asignar en masa
    protected $fillable = [
        'designacion_id',
        'fecha_salida',
        'hora_salida',
        'estado_salida',
        'motivo_no_salida',
        'kilometraje_salida',
        'kilometraje_llegada',
        'tipo_mantenimiento',
        'status_mantenimiento',
        'conductor_confirmado',
        'anfitrion_confirmado',





    ];





    // Si también deseas definir la relación con la designación de bus
    public function designacionBus()
    {
        return $this->belongsTo(AsignacionDeBus::class, 'designacion_id');
    }

    /* Relacón con Mantenimiento para ver el estado */
    public function mantenimiento()
    {
        return $this->hasOne(Mantenimiento::class, 'salida_id');
    }


    /* Funcion para crear mantenimiento o actualizar en base a la salida de buses*/

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($salidaDeBus) {
            // Buscar si ya existe un mantenimiento para esta salida
            $mantenimiento = Mantenimiento::where('salida_id', $salidaDeBus->id_salida_bus)->first();

            if ($mantenimiento) {
                // Si ya existe un mantenimiento, solo lo actualizamos
                $mantenimiento->update([
                    'km_anterior' => $salidaDeBus->kilometraje_salida,
                    'km_actual' => $salidaDeBus->kilometraje_llegada,
                    'km_actual_recorrido' => $salidaDeBus->kilometraje_llegada - $salidaDeBus->kilometraje_salida,
                    'tipo_mantenimiento' => $salidaDeBus->tipo_mantenimiento ?? 'Rutina',
                    'estado_mantenimiento' => $salidaDeBus->mantenimiento->estado_mantenimiento ?? 'pendiente',
                    'bus_id' => AsignacionDeBus::find($salidaDeBus->designacion_id)?->id_buses,
                    'tecnico_id' => $salidaDeBus->tecnico_id,
                    'observaciones' => 'Actualizado automáticamente después de una modificación en la salida',
                ]);
            } else {
                // Si no existe un mantenimiento, entonces lo creamos
                $nuevoMantenimiento = Mantenimiento::create([
                    'fecha_mantenimiento' => now(),
                    'km_anterior' => $salidaDeBus->kilometraje_salida,
                    'km_actual' => $salidaDeBus->kilometraje_llegada,
                    'km_actual_recorrido' => $salidaDeBus->kilometraje_llegada - $salidaDeBus->kilometraje_salida,
                    'tipo_mantenimiento' => $salidaDeBus->tipo_mantenimiento ?? 'Rutina',
                    'estado_mantenimiento' => 'pendiente',
                    'generado_por' => 'salida',
                    'salida_id' => $salidaDeBus->id_salida_bus,
                    'bus_id' => AsignacionDeBus::find($salidaDeBus->designacion_id)?->id_buses,
                    'tecnico_id' => $salidaDeBus->tecnico_id,
                    'observaciones' => 'Generado automáticamente después de una salida',
                ]);

                // 🔔 Enviar Notificación en Filament
                Notification::make()
                    ->title('Nuevo Mantenimiento Registrado')
                    ->body("Se ha generado un mantenimiento para el bus {$nuevoMantenimiento->bus_id->numero_bus}.")
                    ->success()
                    ->icon('heroicon-o-wrench') // Icono de llave inglesa para mantenimiento
                    ->persistent()
                    ->send();
            }
        });
    }
}
