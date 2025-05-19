<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use App\Models\EntregaTalonario;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class CreateEntregaTalonario extends CreateRecord
{
    protected static string $resource = EntregaTalonarioResource::class;

   protected function handleRecordCreation(array $data): Model
{
    try {
        // Valores por defecto si están vacíos
        $data['cantidad_preferenciales'] = $data['cantidad_preferenciales'] ?? 0;
        $data['rango_inicial_preferencial'] = $data['rango_inicial_preferencial'] ?? 0;
        $data['cantidad_regulares'] = $data['cantidad_regulares'] ?? 0;
        $data['rango_inicial_regular'] = $data['rango_inicial_regular'] ?? 0;
        $data['fecha_entrega'] = $data['fecha_entrega'] ?? now()->format('Y-m-d');
        $data['observaciones'] = $data['observaciones'] ?? '';

        DB::statement('CALL entregar_talonarios(?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['cajero_id'],
            $data['inventario_id'],
            $data['cantidad_preferenciales'],
            $data['rango_inicial_preferencial'],
            $data['cantidad_regulares'],
            $data['rango_inicial_regular'],
            $data['fecha_entrega'],
            $data['observaciones'],
        ]);


        return EntregaTalonario::latest('id')->first();

    } catch (QueryException $e) {
        Notification::make()
            ->title('Error al realizar la entrega')
            ->body($e->getMessage())
            ->danger()
            ->send();

        throw $e;
    }
}

}