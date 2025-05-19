<?php

namespace App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource;
use App\Models\EntregaTalonariosAnfitrion;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class CreateEntregaTalonariosAnfitrion extends CreateRecord
{
    protected static string $resource = EntregaTalonariosAnfitrionResource::class;

   protected function handleRecordCreation(array $data): Model
{
    try {
        $data['anfitrion_id'] = $data['anfitrion_id'] ?? null;
        $data['entrega_talonario_id'] = $data['recibido_por'] ?? null;
        $data['numero_autorizacion'] = $data['numero_autorizacion'] ?? '';
        $data['cantidad_talonarios_preferenciales'] = $data['cantidad_talonarios_preferenciales'] ?? 0;
        $data['rango_inicial_preferenciales'] = $data['rango_inicial_preferenciales'] ?? 0;
        $data['cantidad_talonarios_regulares'] = $data['cantidad_talonarios_regulares'] ?? 0;
        $data['rango_inicial_regulares'] = $data['rango_inicial_regulares'] ?? 0;
        $data['fecha_entrega'] = $data['fecha_entrega'] ?? now()->format('Y-m-d');
        $data['observaciones'] = $data['observaciones'] ?? '';

        DB::statement('CALL entregar_talonarios_anfitrion(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['entrega_talonario_id'],
            $data['anfitrion_id'],
            $data['numero_autorizacion'],
            $data['cantidad_talonarios_preferenciales'],
            $data['rango_inicial_preferenciales'],
            $data['cantidad_talonarios_regulares'],
            $data['rango_inicial_regulares'],
            $data['fecha_entrega'],
            $data['observaciones'],
        ]);

        // Si quieres devolver el modelo creado, deberías crear una consulta correcta aquí:
        return EntregaTalonariosAnfitrion::latest('id')->first();

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
