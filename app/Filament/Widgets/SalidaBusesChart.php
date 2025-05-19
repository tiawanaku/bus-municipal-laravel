<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SalidaDeBuses;
use Carbon\Carbon;

class SalidaBusesChart extends ChartWidget
{
    protected static ?string $heading = 'Salidas de Buses durante la semana';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        Carbon::setLocale('es');
        $labels = [];
        /* función para iterar cada día de la semana actual guardando en un array  */

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $labels[] = $fecha->isoFormat('D [de] MMMM');

            $salidas[] = SalidaDeBuses::whereDate('fecha_salida', $fecha)
                ->where('estado_salida', 'salida')
                ->count();

            $noSalidas[] = SalidaDeBuses::whereDate('fecha_salida', $fecha)
                ->where('estado_salida', 'no_salida')
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Salidas',
                    'data' => $salidas,
                    'borderColor' => '#0B4A9B',
                    'backgroundColor' => 'rgba(11, 74, 155, 0.2)',
                ],
                [
                    'label' => 'No salidas',
                    'data' => $noSalidas,
                    'borderColor' => '#E4005F',
                    'backgroundColor' => 'rgba(228, 0, 95, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];

    }

    protected function getType(): string
    {
        return 'line';
    }
}
