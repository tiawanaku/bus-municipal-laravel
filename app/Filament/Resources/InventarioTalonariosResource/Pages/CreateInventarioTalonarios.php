<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Pages;

use App\Filament\Resources\InventarioTalonariosResource;
use App\Models\InventarioTalonarios;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CreateInventarioTalonarios extends CreateRecord
{
    protected static string $resource = InventarioTalonariosResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Preparar parámetros con valores por defecto
        $params = [
            'cajero_id' => (int) ($data['cajero_id'] ?? 1),
            'preferencial_del' => (int) ($data['preferencial_del'] ?? 1),
            'preferencial_al' => (int) ($data['preferencial_al'] ?? 1),
            'regular_del' => (int) ($data['regular_del'] ?? 1),
            'regular_al' => (int) ($data['regular_al'] ?? 1),
            'rango_inicial_preferencial' => (int) ($data['rango_inicial_preferencial'] ?? 1),
            'rango_inicial_regular' => (int) ($data['rango_inicial_regular'] ?? 1),
            'fecha_entrega' => $data['fecha_entrega'] ?? now()->format('Y-m-d'),
            'observaciones' => $data['observaciones'] ?? '',
            'n_cite' => $data['n_cite'] ?? '001',
            'gestion' => $data['gestion'] ?? date('Y'),
            'n_dosificacion' => $data['n_dosificacion'] ?? '',
            'numero_autorizacion' => $data['numero_autorizacion'] ?? '',
            'fecha_solicitud_dosificacion' => $data['fecha_solicitud_dosificacion'] ?? null,
            'fecha_autorizacion' => $data['fecha_autorizacion'] ?? null,
            'fecha_activacion' => $data['fecha_activacion'] ?? null,
            'cite_nota_solicitud' => $data['cite_nota_solicitud'] ?? '',
            'tipo_talonarios' => $data['tipo_talonarios'] ?? 'ambos', // AÑADIR ESTA LÍNEA
        ];

        // Convertir fechas a string si son objetos
        foreach (['fecha_entrega', 'fecha_solicitud_dosificacion', 'fecha_autorizacion', 'fecha_activacion'] as $dateField) {
            if ($params[$dateField] instanceof \DateTime) {
                $params[$dateField] = $params[$dateField]->format('Y-m-d');
            }
        }

        // Manejar valores nulos para fechas
        $fechaSolicitud = $params['fecha_solicitud_dosificacion'] ?: null;
        $fechaAutorizacion = $params['fecha_autorizacion'] ?: null;
        $fechaActivacion = $params['fecha_activacion'] ?: null;

        try {
            // CORRECCIÓN: Llamar al procedimiento almacenado con 18 parámetros de entrada + 2 de salida
            DB::statement('CALL SP_CrearInventarioPrincipal(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @resultado, @mensaje)', [
                $params['cajero_id'],                    // 1. p_cajero_id
                $params['preferencial_del'],             // 2. p_preferencial_del
                $params['preferencial_al'],              // 3. p_preferencial_al
                $params['regular_del'],                  // 4. p_regular_del
                $params['regular_al'],                   // 5. p_regular_al
                $params['rango_inicial_preferencial'],   // 6. p_rango_inicial_preferencial
                $params['rango_inicial_regular'],        // 7. p_rango_inicial_regular
                $params['fecha_entrega'],                // 8. p_fecha_entrega
                $params['observaciones'],                // 9. p_observaciones
                $params['n_cite'],                       // 10. p_n_cite
                $params['gestion'],                      // 11. p_gestion
                $params['n_dosificacion'],               // 12. p_n_dosificacion
                $params['numero_autorizacion'],          // 13. p_numero_autorizacion
                $fechaSolicitud,                         // 14. p_fecha_solicitud_dosificacion
                $fechaAutorizacion,                      // 15. p_fecha_autorizacion
                $fechaActivacion,                        // 16. p_fecha_activacion
                $params['cite_nota_solicitud'],          // 17. p_cite_nota_solicitud
                $params['tipo_talonarios'],              // 18. p_tipo_talonarios (NUEVO PARÁMETRO)
            ]);

            // Obtener los valores de salida
            $output = DB::select('SELECT @resultado as resultado, @mensaje as mensaje');
            $resultado = $output[0]->resultado;
            $mensaje = $output[0]->mensaje;

            if (!$resultado || $resultado === 0) {
                throw new \Exception($mensaje ?: 'Error desconocido al crear inventario');
            }

            return InventarioTalonarios::findOrFail($resultado);

        } catch (\Exception $e) {
            // Log para debugging
            logger()->error('Error en SP_CrearInventarioPrincipal', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            
            throw new \Exception('Error al crear el inventario: ' . $e->getMessage());
        }
    }
}