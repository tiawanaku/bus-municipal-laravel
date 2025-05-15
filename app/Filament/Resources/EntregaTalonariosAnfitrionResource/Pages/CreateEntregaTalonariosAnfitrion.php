<?php

namespace App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateEntregaTalonariosAnfitrion extends CreateRecord
{
    protected static string $resource = EntregaTalonariosAnfitrionResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        // Extraer y validar los campos necesarios
        $anfitrion_id = $data['anfitrion_id'] ?? null;
        $recibido_por = $data['recibido_por'] ?? null;
        $numero_autorizacion = $data['numero_autorizacion'] ?? null;
        $cantidad_pref = $data['cantidad_talonarios_preferenciales'] ?? null;
        $rango_ini_pref = $data['rango_inicial_preferenciales'] ?? null;
        $cantidad_reg = $data['cantidad_talonarios_regulares'] ?? null;
        $rango_ini_reg = $data['rango_inicial_regulares'] ?? null;

        if (
            is_null($anfitrion_id) || is_null($recibido_por) || is_null($numero_autorizacion) ||
            is_null($cantidad_pref) || is_null($rango_ini_pref) ||
            is_null($cantidad_reg) || is_null($rango_ini_reg)
        ) {
            throw new \Exception('Faltan datos obligatorios para registrar la entrega.');
        }

        // Llamar al nuevo procedimiento almacenado con los 7 parámetros
        DB::statement('CALL registrar_entrega_anfitrion(?, ?, ?, ?, ?, ?, ?)', [
            $anfitrion_id,
            $recibido_por,
            $numero_autorizacion,
            $cantidad_pref,
            $rango_ini_pref,
            $cantidad_reg,
            $rango_ini_reg,
        ]);

        // Cancelar la creación automática ya que el procedimiento hace el insert
        $this->halt();
    }
}
