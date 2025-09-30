<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use App\Models\EntregaTalonario;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateEntregaTalonario extends CreateRecord
{
    protected static string $resource = EntregaTalonarioResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Preparar parámetros directamente sin validaciones complejas
        $params = [
            'inventario_id' => (int) ($data['inventario_id'] ?? 0),
            'cajero_id' => (int) ($data['cajero_id'] ?? 0),
            'preferencial_del' => (int) ($data['preferencial_del'] ?? 0),
            'preferencial_al' => (int) ($data['preferencial_al'] ?? 0),
            'regular_del' => (int) ($data['regular_del'] ?? 0),
            'regular_al' => (int) ($data['regular_al'] ?? 0),
            'rango_inicial_preferencial' => (int) ($data['rango_inicial_preferencial'] ?? 0),
            'rango_inicial_regular' => (int) ($data['rango_inicial_regular'] ?? 0),
            'observaciones' => $data['observaciones'] ?? '',
        ];

        // Log para debugging
        logger()->info('=== PARÁMETROS PARA SP ===', $params);

        try {
            // Llamar al procedimiento almacenado
            DB::statement('CALL SP_EntregarTalonariosCajero_UniInventario(?, ?, ?, ?, ?, ?, ?, ?, ?, @resultado, @mensaje)', [
                $params['inventario_id'],
                $params['cajero_id'],
                $params['preferencial_del'],
                $params['preferencial_al'],
                $params['regular_del'],
                $params['regular_al'],
                $params['rango_inicial_preferencial'],
                $params['rango_inicial_regular'],
                $params['observaciones']
            ]);

            // Obtener valores de salida
            $output = DB::select('SELECT @resultado as resultado, @mensaje as mensaje');
            $resultado = $output[0]->resultado;
            $mensaje = $output[0]->mensaje;

            logger()->info("SP Resultado: {$resultado}, Mensaje: {$mensaje}");

            if (!$resultado || $resultado === 0) {
                throw new \Exception($mensaje ?: 'Error desconocido al crear entrega');
            }

            Notification::make()
                ->title('Entrega Exitosa')
                ->body($mensaje)
                ->success()
                ->send();

            return EntregaTalonario::findOrFail($resultado);

        } catch (\Exception $e) {
            logger()->error('Error en SP_EntregarTalonariosCajero_UniInventario', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            Notification::make()
                ->title('Error al crear entrega')
                ->body($e->getMessage())
                ->danger()
                ->send();
            
            throw new \Exception('Error al crear la entrega: ' . $e->getMessage());
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}