<?php

namespace App\Filament\Resources\FormularioRecaudoResource\Pages;

use App\Filament\Resources\FormularioRecaudoResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateFormularioRecaudo extends CreateRecord
{
    protected static string $resource = FormularioRecaudoResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['cantidad_ventas_regulares'] = $data['cantidad_ventas_regulares'] ?? 0;
        $data['rango_inicial_regulares'] = $data['rango_inicial_regulares'] ?? 0;
        $data['cantidad_ventas_preferenciales'] = $data['cantidad_ventas_preferenciales'] ?? 0;
        $data['rango_inicial_preferencial'] = $data['rango_inicial_preferencial'] ?? 0;

        // Necesitas obtener el ID del talonario del anfitrión
        $talonarioId = DB::table('entrega_talonarios_anfitrion')
            ->where('anfitrion_id', $data['anfitrion_id'])
            ->where('estado', 'activo')
            ->value('id') ?? 1;

        DB::statement("CALL SP_RegistrarVentaTickets(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @resultado, @mensaje)", [
            $data['anfitrion_id'],                  // p_anfitrion_id
            $data['conductor_id'],                  // p_conductor_id
            $data['bus_id'],                        // p_bus_id
            $talonarioId,                           // p_entrega_talonario_anfitrion_id
            $data['cantidad_ventas_regulares'],     // p_cantidad_ventas_regulares
            $data['rango_inicial_regulares'],       // p_rango_inicial_regulares
            $data['cantidad_ventas_preferenciales'],// p_cantidad_ventas_preferenciales
            $data['rango_inicial_preferencial'],    // p_rango_inicial_preferencial
            $data['rutas'],                         // p_rutas
            $data['horario'],                       // p_horario
            $data['N_ficha'],                       // p_n_ficha
            now()->format('Y-m-d'),                 // p_fecha_recaudo
            $data['observaciones'] ?? '',           // p_observaciones
        ]);

        // Obtener los resultados del procedimiento
        $result = DB::select('SELECT @resultado as resultado, @mensaje as mensaje');
        
        if ($result[0]->resultado === 0) {
            throw new \Exception($result[0]->mensaje);
        }

        return static::getModel()::find($result[0]->resultado) ?? static::getModel()::make();
    }
}