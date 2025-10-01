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
        // USAR VALORES POR DEFECTO PARA EVITAR EL ERROR
        DB::statement('CALL actualizar_entrega_talonarios_completa(?, ?, ?, ?, ?)', [
            $this->record->id,            // p_entrega_id
            $data['cantidad_preferenciales'] ?? 0,  // p_cantidad_pref
            $data['rango_inicial_preferencial'] ?? 0, // p_rango_inicial_pref
            $data['cantidad_regulares'] ?? 0,        // p_cantidad_reg
            $data['rango_inicial_regular'] ?? 0,     // p_rango_inicial_reg
        ]);

        return $data;
    }
}