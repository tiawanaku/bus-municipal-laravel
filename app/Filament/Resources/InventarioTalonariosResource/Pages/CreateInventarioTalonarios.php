<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Pages;

use App\Filament\Resources\InventarioTalonariosResource;
use App\Models\InventarioTalonarios;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CreateInventarioTalonarios extends CreateRecord
{
    protected static string $resource = InventarioTalonariosResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        // Asegurar valores predeterminados si vienen vacíos
        $data['cantidad_preferenciales'] = $data['cantidad_preferenciales'] ?? 0;
        $data['rango_inicial_preferencial'] = $data['rango_inicial_preferencial'] ?? 0;
        $data['cantidad_regulares'] = $data['cantidad_regulares'] ?? 0;
        $data['rango_inicial_regular'] = $data['rango_inicial_regular'] ?? 0;
        $data['fecha_entrega'] = $data['fecha_entrega'] ?? now();
        $data['observaciones'] = $data['observaciones'] ?? '';

        // Llamar al procedimiento almacenado
        DB::statement('CALL inventario_talonarios(?, ?, ?, ?, ?, ?, ?)', [
            $data['cajero_id'],
            $data['cantidad_preferenciales'],
            $data['rango_inicial_preferencial'],
            $data['cantidad_regulares'],
            $data['rango_inicial_regular'],
            $data['fecha_entrega'],
            $data['observaciones'] ?? null,
        ]);

        // Retornar el último registro insertado (opcional)
        return InventarioTalonarios::latest('created_at')->first();
    }
}
