<?php

namespace App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource;
use App\Models\EntregaTalonariosAnfitrion;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateEntregaTalonariosAnfitrion extends CreateRecord
{
    protected static string $resource = EntregaTalonariosAnfitrionResource::class;

   protected function handleRecordCreation(array $data): Model
{
    try {
        \Log::info('=== INICIO CREACIÓN ENTREGA ANFITRIÓN ===');
        \Log::info('Datos recibidos:', $data);

        // ✅ Usar NULL en lugar de 0 cuando no existen
        $preferencialDel = isset($data['preferencial_Del']) && $data['preferencial_Del'] !== '' 
            ? (int)$data['preferencial_Del'] 
            : null;
            
        $preferencialAl = isset($data['preferencial_Al']) && $data['preferencial_Al'] !== '' 
            ? (int)$data['preferencial_Al'] 
            : null;
            
        $regularDel = isset($data['regular_Del']) && $data['regular_Del'] !== '' 
            ? (int)$data['regular_Del'] 
            : null;
            
        $regularAl = isset($data['regular_Al']) && $data['regular_Al'] !== '' 
            ? (int)$data['regular_Al'] 
            : null;
            
        $rangoInicialPreferencial = isset($data['rango_inicial_preferencial']) && $data['rango_inicial_preferencial'] !== '' 
            ? (int)$data['rango_inicial_preferencial'] 
            : null;
            
        $rangoInicialRegular = isset($data['rango_inicial_regular']) && $data['rango_inicial_regular'] !== '' 
            ? (int)$data['rango_inicial_regular'] 
            : null;
            
        $observaciones = $data['observaciones'] ?? '';
        $tipoTalonarios = $data['tipo_talonarios'] ?? '';

        \Log::info('Parámetros preparados:', [
            'entrega_talonario_id' => $data['entrega_talonario_id'],
            'anfitrion_id' => $data['anfitrion_id'],
            'preferencial_Del' => $preferencialDel,
            'preferencial_Al' => $preferencialAl,
            'regular_Del' => $regularDel,
            'regular_Al' => $regularAl,
            'rango_inicial_preferencial' => $rangoInicialPreferencial,
            'rango_inicial_regular' => $rangoInicialRegular,
            'tipo_talonarios' => $tipoTalonarios
        ]);

        // Llamar al procedimiento almacenado
        DB::statement('CALL SP_EntregarTalonariosAnfitrion(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @resultado, @mensaje)', [
            $data['entrega_talonario_id'],
            $data['anfitrion_id'],
            $preferencialDel,
            $preferencialAl,
            $regularDel,
            $regularAl,
            $rangoInicialPreferencial,
            $rangoInicialRegular,
            $observaciones,
            $tipoTalonarios
        ]);

        // Obtener resultado
        $output = DB::select('SELECT @resultado as resultado, @mensaje as mensaje');
        $resultado = $output[0]->resultado;
        $mensaje = $output[0]->mensaje;

        \Log::info('Resultado SP:', [
            'resultado' => $resultado,
            'mensaje' => $mensaje
        ]);

        if ($resultado > 0) {
            $entregaAnfitrion = EntregaTalonariosAnfitrion::find($resultado);
            
            if (!$entregaAnfitrion) {
                throw new \Exception('No se pudo recuperar el registro creado');
            }
            
            if (!$entregaAnfitrion->created_at || !$entregaAnfitrion->updated_at) {
                $now = now();
                $entregaAnfitrion->update([
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $entregaAnfitrion->refresh();
            }
            
            Notification::make()
                ->title('Entrega creada exitosamente')
                ->body($mensaje)
                ->success()
                ->send();

            return $entregaAnfitrion;
        } else {
            throw new \Exception($mensaje);
        }

    } catch (\Exception $e) {
        \Log::error('ERROR:', [
            'mensaje' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        Notification::make()
            ->title('Error al crear la entrega')
            ->body($e->getMessage())
            ->danger()
            ->send();

        throw $e;
    }
}

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
