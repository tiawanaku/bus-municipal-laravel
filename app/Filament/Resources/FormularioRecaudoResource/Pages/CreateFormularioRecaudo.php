<?php

namespace App\Filament\Resources\FormularioRecaudoResource\Pages;

use App\Filament\Resources\FormularioRecaudoResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateFormularioRecaudo extends CreateRecord
{
    protected static string $resource = FormularioRecaudoResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Ejecutar el procedimiento almacenado con los parámetros correctos
        DB::statement("CALL registrar_recaudo(?, ?, ?, ?, ?, ?, ?, ?)", [
            $data['anfitrion_id'],
            $data['bus_id'],
            $data['conductor_id'],
            $data['ruta_id'],
            $data['horario'],
            $data['cantidad_ventas_regulares'],
            $data['cantidad_ventas_preferenciales'],
            $data['observaciones'] ?? '',
        ]);

        // Retornar el último registro insertado o uno nuevo si no existe
        return static::getModel()::query()->latest()->first() ?? static::getModel()::make();
    }
}
