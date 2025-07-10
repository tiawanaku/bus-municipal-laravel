<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Notifications\Notification;



class SalidaDeBuses extends Model
{
    use HasFactory;
    use SoftDeletes;


    // Nombre de la tabla si es diferente del plural del modelo
    protected $table = 'salida_de_buses';

    protected $primaryKey = 'id_salida_bus';

    // Los campos que se pueden asignar en masa
    protected $fillable = [
        'designacion_id',
        'ruta_id',
        'horario_id',
        'fecha_salida',
        'hora_salida',
        'fecha_llegada',
        'hora_llegada',
        'estado_salida',
        'motivo_no_salida',
        'kilometraje_salida',
        'kilometraje_llegada',
        'tipo_mantenimiento',
        'status_mantenimiento',
        'conductor_confirmado',
        'anfitrion_confirmado',





    ];





    // relación con la designación de bus
    public function designacionBus()
    {
        return $this->belongsTo(AsignacionDeBus::class, 'designacion_id');
    }
    /* Relación con Ruta */
    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    /* Relacón con Mantenimiento para ver el estado */
    public function mantenimiento()
    {
        return $this->hasOne(Mantenimiento::class, 'salida_id');
    }
    /* Relación con Suplantacion anfitrion */
    public function suplantacionAnfitrion()
    {
        return $this->hasOne(SuplantacionAnfitrion::class, 'salida_bus_id', 'id_salida_bus');
    }
    /* Relación con suplantacion conductor */
    public function suplantacionConductor()
    {
        return $this->hasOne(SuplantacionConductor::class, 'salida_bus_id', 'id_salida_bus');
    }
    /* Relacion con horarios */
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    /* Funcion para crear mantenimiento o actualizar en base a la salida de buses*/

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($salidaDeBus) {
            // Solo ejecutar si ya hay kilometraje de llegada
            if (is_null($salidaDeBus->kilometraje_llegada)) {
                return;
            }

            $busAsignado = AsignacionDeBus::find($salidaDeBus->designacion_id)?->id_buses;

            if (!$busAsignado) {
                // No se pudo determinar el bus, salir
                return;
            }

            $mantenimiento = Mantenimiento::where('salida_id', $salidaDeBus->id_salida_bus)->first();

            $datos = [
                'km_anterior' => $salidaDeBus->kilometraje_salida,
                'km_actual' => $salidaDeBus->kilometraje_llegada,
                'km_actual_recorrido' => $salidaDeBus->kilometraje_llegada - $salidaDeBus->kilometraje_salida,
                'tipo_mantenimiento' => $salidaDeBus->tipo_mantenimiento ?? 'Rutina',
                'bus_id' => $busAsignado,
                'tecnico_id' => $salidaDeBus->tecnico_id,
            ];

            if ($mantenimiento) {
                $mantenimiento->update(array_merge($datos, [
                    'estado_mantenimiento' => $mantenimiento->estado_mantenimiento ?? 'pendiente',
                    'observaciones' => 'Actualizado automáticamente después de una modificación en la salida',
                ]));
            } else {
                $nuevoMantenimiento = Mantenimiento::create(array_merge($datos, [
                    'fecha_mantenimiento' => now(),
                    'estado_mantenimiento' => 'pendiente',
                    'generado_por' => 'salida',
                    'salida_id' => $salidaDeBus->id_salida_bus,
                    'observaciones' => 'Generado automáticamente después de una salida',
                ]));

                // Notificación
                $usuarios = \App\Models\User::all();

                Notification::make()
                    ->title('Nuevo Mantenimiento Registrado')
                    ->body("Se ha generado un mantenimiento para el bus {$nuevoMantenimiento->bus->numero_bus}.")
                    ->success()
                    ->icon('heroicon-o-wrench')
                    ->persistent()
                    ->send()
                    ->sendToDatabase($usuarios);
            }
        });


    }

}
