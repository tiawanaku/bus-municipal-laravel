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
        // Inicialización por defecto (igual que antes)
        $data['cantidad_preferenciales'] = $data['cantidad_preferenciales'] ?? 0;
        $data['rango_inicial_preferencial'] = $data['rango_inicial_preferencial'] ?? 0;
        $data['preferencial_del'] = $data['preferencial_del'] ?? 0;
        $data['preferencial_al'] = $data['preferencial_al'] ?? 0;

        $data['cantidad_regulares'] = $data['cantidad_regulares'] ?? 0;
        $data['rango_inicial_regular'] = $data['rango_inicial_regular'] ?? 0;
        $data['regular_del'] = $data['regular_del'] ?? 0;
        $data['regular_al'] = $data['regular_al'] ?? 0;

        $data['fecha_entrega'] = $data['fecha_entrega'] ?? now();
        $data['observaciones'] = $data['observaciones'] ?? '';

        $data['cite_nota_solicitud'] = $data['cite_nota_solicitud'] ?? null;
        $data['cite_nota_solicitud_filename'] = $data['cite_nota_solicitud_filename'] ?? null;
        $data['n_cite'] = $data['n_cite'] ?? null;
        $data['gestion'] = $data['gestion'] ?? null;
        $data['n_dosificacion'] = $data['n_dosificacion'] ?? null;
        $data['numero_autorizacion'] = $data['numero_autorizacion'] ?? null;
        $data['fecha_solicitud_dosificacion'] = $data['fecha_solicitud_dosificacion'] ?? null;
        $data['fecha_autorizacion'] = $data['fecha_autorizacion'] ?? null;
        $data['fecha_activacion'] = $data['fecha_activacion'] ?? null;

        DB::statement('CALL inventario_talonarios(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['cajero_id'],
            $data['cantidad_preferenciales'],
            $data['rango_inicial_preferencial'],
            $data['cantidad_regulares'],
            $data['rango_inicial_regular'],
            $data['fecha_entrega'],
            $data['observaciones'],
            $data['cite_nota_solicitud'],
            $data['cite_nota_solicitud_filename'],
            $data['n_cite'],
            $data['gestion'],
            $data['n_dosificacion'],
            $data['numero_autorizacion'],
            $data['fecha_solicitud_dosificacion'],
            $data['fecha_autorizacion'],
            $data['fecha_activacion'],
            $data['regular_del'],
            $data['regular_al'],
            $data['preferencial_del'],
            $data['preferencial_al'],
        ]);

        // ✅ Retornar el último insertado (opcional)
        return InventarioTalonarios::latest()->first();
    }
}
