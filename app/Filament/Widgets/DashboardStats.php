<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\SalidaDeBuses;
use App\Models\Aviso;
use App\Models\Mantenimiento;
use App\Models\AsignacionDeBus;
use Carbon\Carbon;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        return [
            Stat::make('Salidas hoy', SalidaDeBuses::whereDate('fecha_salida', today())->count())
                ->description('Total de salidas registradas hoy')
                ->descriptionIcon('heroicon-o-truck')
                ->color('primary'),

            Stat::make(
                'Avisos activos',
                Aviso::where('status', true)
                    ->whereDate('fin_periodo', '>=', Carbon::today())
                    ->count()
            )
                ->description('Avisos activos no vencidos')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('danger'),

            Stat::make('Mantenimientos este mes', Mantenimiento::whereMonth('fecha_mantenimiento', now()->month)->count())
                ->description('Mantenimientos realizados')
                ->descriptionIcon('heroicon-o-wrench')
                ->color('warning'),

            Stat::make('Buses asignados', AsignacionDeBus::whereNotNull('id_designacion_bus')->count())
                ->description('Buses actualmente en uso')
                ->color('success'),
        ];
    }
}
