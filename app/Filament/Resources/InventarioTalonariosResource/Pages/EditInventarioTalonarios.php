<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Pages;

use App\Filament\Resources\InventarioTalonariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditInventarioTalonarios extends EditRecord
{
    protected static string $resource = InventarioTalonariosResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;

       DB::statement('CALL actualizar_inventario_talonarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
             $record->id,
    $record->cajero_id,
    $record->rango_inicial_preferencial,
    $record->rango_inicial_regular,
    $record->fecha_entrega,
    $record->observaciones,

            // ✅ Nuevos parámetros agregados:
            $record->regular_del,
            $record->regular_al,
            $record->preferencial_del,
            $record->preferencial_al,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
