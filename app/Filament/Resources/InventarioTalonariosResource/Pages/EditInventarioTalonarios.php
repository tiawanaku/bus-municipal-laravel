<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Pages;

use App\Filament\Resources\InventarioTalonariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class EditInventarioTalonarios extends EditRecord
{
    protected static string $resource = InventarioTalonariosResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;

        DB::statement('CALL actualizar_inventario_talonarios(?, ?, ?, ?, ?, ?, ?, ?)', [
            $record->id,
            $record->cajero_id,
            $record->cantidad_preferenciales,
            $record->rango_inicial_preferencial,
            $record->cantidad_regulares,
            $record->rango_inicial_regular,
            $record->fecha_entrega,
            $record->observaciones,
        ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

        ];
    }
}
