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

    DB::statement("CALL registrar_formulario_recaudo(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        $data['bus_id'],                        // p_bus_id
        $data['conductor_id'],                  // p_conductor_id
        $data['anfitrion_id'],                  // p_anfitrion_id
        $data['rutas'],                         // p_rutas
        $data['horario'],                       // p_horario
        $data['cantidad_ventas_regulares'],     // p_cantidad_ventas_regulares
        $data['rango_inicial_regulares'],        // p_rango_inicial_regulares
        $data['cantidad_ventas_preferenciales'],// p_cantidad_ventas_preferenciales
        $data['rango_inicial_preferencial'],   // p_rango_inicial_preferencial
        $data['N_ficha'],                       // p_n_ficha
    ]);

    return static::getModel()::query()->latest()->first() ?? static::getModel()::make();

    }
}
