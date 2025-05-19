<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditEntregaTalonario extends EditRecord
{
    protected static string $resource = EntregaTalonarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Aquí puedes llamar al procedimiento almacenado para actualizar la entrega
        DB::statement('CALL actualizar_entrega_talonarios_completa(?, ?, ?, ?, ?)', [
            $this->record->id,            // p_entrega_id
            $data['cantidad_preferenciales'],  // p_cantidad_pref
            $data['rango_inicial_preferencial'], // p_rango_inicial_pref
            $data['cantidad_regulares'],        // p_cantidad_reg
            $data['rango_inicial_regular'],    // p_rango_inicial_reg
        ]);

        // Puedes retornar $data si quieres que Filament continúe con el guardado normal
        // O retornar un arreglo vacío o modificado si quieres controlar el guardado manualmente
        return $data;
    }
}