<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Pages;

use App\Filament\Resources\InventarioTalonariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventarioTalonarios extends EditRecord
{
    protected static string $resource = InventarioTalonariosResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Solo calcular si los campos existen
        if (isset($data['preferencial_al'], $data['preferencial_del'], 
            $data['regular_al'], $data['regular_del'])) {
            
            $cantidadPreferenciales = $data['preferencial_al'] - $data['preferencial_del'] + 1;
            $cantidadRegulares = $data['regular_al'] - $data['regular_del'] + 1;
            
            if (isset($data['rango_inicial_preferencial'])) {
                $data['rango_final_preferencial'] = $data['rango_inicial_preferencial'] + ($cantidadPreferenciales * 50) - 1;
            }
            
            if (isset($data['rango_inicial_regular'])) {
                $data['rango_final_regular'] = $data['rango_inicial_regular'] + ($cantidadRegulares * 50) - 1;
            }
            
            $data['total_boletos_preferenciales'] = $cantidadPreferenciales * 50;
            $data['total_boletos_regulares'] = $cantidadRegulares * 50;
            $data['total_aproximado_bolivianos_preferencial'] = $data['total_boletos_preferenciales'] * 1.00;
            $data['total_aproximado_bolivianos_regular'] = $data['total_boletos_regulares'] * 1.50;
            $data['total_recaudacion_bolivianos'] = $data['total_aproximado_bolivianos_preferencial'] + $data['total_aproximado_bolivianos_regular'];
            $data['cantidad_restante_preferencial'] = $cantidadPreferenciales;
            $data['cantidad_restante_regular'] = $cantidadRegulares;
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}