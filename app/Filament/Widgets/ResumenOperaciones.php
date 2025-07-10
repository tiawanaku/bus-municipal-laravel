<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\SalidaDeBuses;
use Carbon\Carbon;

class ResumenOperaciones extends BaseWidget
{
    protected function getStats(): array
    {
        $hoy = Carbon::today();

        $totalSalidasHoy = SalidaDeBuses::whereDate('fecha_salida', $hoy)
            ->where('estado_salida', 'salida')
            ->count();

        $busesEnRuta = SalidaDeBuses::where('estado_salida', 'salida')
            ->whereDate('fecha_salida', $hoy)
            ->whereNotNull('kilometraje_salida')
            ->whereNull('kilometraje_llegada')
            ->count();

        $totalNoSalidasHoy = SalidaDeBuses::whereDate('fecha_salida', $hoy)
            ->where('estado_salida', 'no_salida')
            ->count();

        return [
            Stat::make('Salidas hoy', $totalSalidasHoy)
                ->description('Cantidad de buses que salieron hoy')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Buses en ruta', $busesEnRuta)
                ->description('Buses que actualmente estan en ruta')
                ->icon('heroicon-o-truck'),

            Stat::make('No salidas', $totalNoSalidasHoy)
                ->description('Total de Buses que no salieron hoy')
                ->icon('heroicon-o-chart-bar'),
        ];

    }
    public static function canView(): bool
    {
        return request()->routeIs('filament.pages.operaciones-dashboard');
    }
}

