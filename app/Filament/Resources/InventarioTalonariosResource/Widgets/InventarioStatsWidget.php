<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Widgets;

use App\Models\InventarioTalonarios;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarioStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Obtener primer registro disponible
        $talonarioDisponible = InventarioTalonarios::where('estado', 'disponible')
            ->orderBy('id')
            ->first();

        $totalPreferenciales = $talonarioDisponible->cantidad_preferenciales ?? 0;
        $restantePreferenciales = $talonarioDisponible->cantidad_restante_preferencial ?? 0;
        $progresoPreferenciales = $totalPreferenciales > 0 ? ($restantePreferenciales / $totalPreferenciales) * 100 : 0;

        $totalRegulares = $talonarioDisponible->cantidad_regulares ?? 0;
        $restanteRegulares = $talonarioDisponible->cantidad_restante_regular ?? 0;
        $progresoRegulares = $totalRegulares > 0 ? ($restanteRegulares / $totalRegulares) * 100 : 0;

        // Recaudación solo de registros disponibles
        $recaudacion = InventarioTalonarios::where('estado', 'disponible')
            ->sum('total_recaudacion_bolivianos');

        // Obtener la primera dosificación de registros disponibles
        $primeraDosificacion = InventarioTalonarios::where('estado', 'disponible')
            ->orderBy('id')
            ->value('n_dosificacion') ?? 'No registrada';

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