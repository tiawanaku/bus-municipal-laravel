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
            $data['preferencial_del'] = $data['preferencial_del'] ?? 0;
            $data['preferencial_al'] = $data['preferencial_al'] ?? 0;
            $data['cantidad_preferenciales'] = $data['cantidad_preferenciales'] ?? 0;
            $data['rango_inicial_preferencial'] = $data['rango_inicial_preferencial'] ?? 0;

            $data['regular_del'] = $data['regular_del'] ?? 0;
            $data['regular_al'] = $data['regular_al'] ?? 0;
            $data['cantidad_regulares'] = $data['cantidad_regulares'] ?? 0;
            $data['rango_inicial_regular'] = $data['rango_inicial_regular'] ?? 0;

            $data['fecha_entrega'] = $data['fecha_entrega'] ?? now()->format('Y-m-d');
            $data['observaciones'] = $data['observaciones'] ?? '';

            DB::statement('CALL entregar_talonarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $data['cajero_id'],
                $data['inventario_id'],
                $data['preferencial_del'],          // agregado
                $data['preferencial_al'],           // agregado
                $data['cantidad_preferenciales'],
                $data['rango_inicial_preferencial'],
                $data['regular_del'],               // agregado
                $data['regular_al'],                // agregado
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
