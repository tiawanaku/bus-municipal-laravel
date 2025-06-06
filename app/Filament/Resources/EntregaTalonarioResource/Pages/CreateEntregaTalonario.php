<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use App\Models\EntregaTalonario;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Carbon\Carbon;


class CreateEntregaTalonario extends CreateRecord
{
    protected static string $resource = EntregaTalonarioResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $data['preferencial_del'] = $data['preferencial_del'] ?? 0;
            $data['preferencial_al'] = $data['preferencial_al'] ?? 0;
            $data['cantidad_preferenciales'] = $data['cantidad_preferenciales'] ?? 0;
            $data['rango_inicial_preferencial'] = $data['rango_inicial_preferencial'] ?? 0;

            $data['regular_del'] = $data['regular_del'] ?? 0;
            $data['regular_al'] = $data['regular_al'] ?? 0;
            $data['cantidad_regulares'] = $data['cantidad_regulares'] ?? 0;
            $data['rango_inicial_regular'] = $data['rango_inicial_regular'] ?? 0;

            $data['fecha_entrega'] = !empty($data['fecha_entrega'])
                ? Carbon::parse($data['fecha_entrega'])->format('Y-m-d')
                : now()->format('Y-m-d');

            $data['observaciones'] = $data['observaciones'] ?? '';
            $data['tipo_talonario'] = $data['tipo_talonario'] ?? '';

            // Debug (opcional)
            // logger()->info('Datos para procedimiento:', $data);

            DB::statement('CALL entregar_talonarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $data['cajero_id'],                  // 1
                $data['inventario_id'],             // 2
                $data['preferencial_del'],          // 3
                $data['preferencial_al'],           // 4
                $data['cantidad_preferenciales'],   // 5
                $data['rango_inicial_preferencial'], // 6
                $data['regular_del'],               // 7
                $data['regular_al'],                // 8
                $data['cantidad_regulares'],        // 9
                $data['rango_inicial_regular'],     // 10
                $data['tipo_talonario'],            // ✅ 11
                $data['fecha_entrega'],             // ✅ 12
                $data['observaciones'],             // ✅ 13
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