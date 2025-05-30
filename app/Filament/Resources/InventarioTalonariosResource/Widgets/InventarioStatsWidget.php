<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Widgets;

use App\Models\InventarioTalonarios;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class InventarioStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Obtener primer registro preferencial activo
        $preferencialActivo = InventarioTalonarios::where('estado_preferencial', 1)
            ->orderBy('id')
            ->first();

        $totalPreferenciales = $preferencialActivo->cantidad_preferenciales ?? 0;
        $restantePreferenciales = $preferencialActivo->cantidad_restante_preferencial ?? 0;
        $progresoPreferenciales = $totalPreferenciales > 0 ? ($restantePreferenciales / $totalPreferenciales) * 100 : 0;

        // Obtener primer registro regular activo
        $regularActivo = InventarioTalonarios::where('estado_regular', 1)
            ->orderBy('id')
            ->first();

        $totalRegulares = $regularActivo->cantidad_regulares ?? 0;
        $restanteRegulares = $regularActivo->cantidad_restante_regular ?? 0;
        $progresoRegulares = $totalRegulares > 0 ? ($restanteRegulares / $totalRegulares) * 100 : 0;

        // Recaudación solo de registros con estado 1
        $recaudacion = InventarioTalonarios::where(function ($query) {
            $query->where('estado_preferencial', 1)
                ->orWhere('estado_regular', 1);
        })->sum('total_recaudacion_bolivianos');

        // Obtener la primera dosificación de registros activos
        $primeraDosificacion = InventarioTalonarios::where(function ($query) {
            $query->where('estado_preferencial', 1)
                ->orWhere('estado_regular', 1);
        })->orderBy('id')->value('n_dosificacion') ?? 'No registrada';

        return [
            Stat::make('Talonarios Preferenciales', $restantePreferenciales)
                ->icon('heroicon-o-ticket')
                ->progress((int) min(100, max(0, $progresoPreferenciales)))
                ->progressBarColor('primary')
                ->description("Total disponibles: $restantePreferenciales de $totalPreferenciales")
                ->descriptionIcon('heroicon-m-ticket', 'before')
                ->descriptionColor('primary')
                ->iconColor('primary'),

            Stat::make('Talonarios Regulares', $restanteRegulares)
                ->icon('heroicon-o-ticket')
                ->progress((int) min(100, max(0, $progresoRegulares)))
                ->progressBarColor('danger')
                ->description("Total disponibles: $restanteRegulares de $totalRegulares")
                ->descriptionIcon('heroicon-m-ticket', 'before')
                ->descriptionColor('danger')
                ->iconColor('danger'),

            Stat::make(
                'Recaudación Total (Bs)',
                number_format($recaudacion, 2, ',', '.')
            )
                ->icon('heroicon-o-currency-dollar')
                ->progressBarColor('warning')
                ->description('Ingresos en bolivianos')
                ->descriptionIcon('heroicon-m-currency-dollar', 'before')
                ->descriptionColor('warning')
                ->iconColor('warning'),

            Stat::make('Primera Dosificación', $primeraDosificacion)
                ->icon('heroicon-o-clipboard-document')
                ->description('Número de Dosificación')
                ->descriptionIcon('heroicon-m-clipboard-document-list', 'before')
                ->descriptionColor('success')
                ->iconColor('success'),
        ];
    }
}