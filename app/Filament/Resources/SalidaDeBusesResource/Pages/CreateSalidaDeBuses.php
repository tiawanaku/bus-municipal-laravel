<?php

namespace App\Filament\Resources\SalidaDeBusesResource\Pages;

use App\Filament\Resources\SalidaDeBusesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\SuplantacionAnfitrion;
use App\Models\SuplantacionConductor;
use App\Models\SalidaDeBuses;
use Illuminate\Database\Eloquent\Model;

class CreateSalidaDeBuses extends CreateRecord
{
    protected static string $resource = SalidaDeBusesResource::class;

    /* Para guardar en la tabla Suplantactión Anfitriones y Conductores*/
    protected function handleRecordCreation(array $data): Model
    {
        // Crear la salida de bus
    $salida = SalidaDeBuses::create($data);

    // Registrar suplencia de anfitrión si no está confirmado
    if (!$data['anfitrion_confirmado']) {
        SuplantacionAnfitrion::create([
            'salida_bus_id' => $salida->id_salida_bus,
            'id_anfitrion_suplente' => $data['anfitrion_manual'],
            'motivo' => $data['motivo_anfitrion'],
        ]);
    }

    // Registrar suplencia de conductor si no está confirmado
    if (!$data['conductor_confirmado']) {
        SuplantacionConductor::create([
            'salida_bus_id' => $salida->id_salida_bus,
            'id_conductor_suplente' => $data['conductor_manual'],
            'motivo' => $data['motivo_conductor'], 
        ]);
    }

    return $salida;
    }
}
