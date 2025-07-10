<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SalidaDeBuses;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Gate;

class SalidaBusesChart extends ChartWidget
{
    protected static ?string $heading = 'Salidas de Buses';
    protected static ?int $sort = 3;



    protected function getData(): array
    {
        $filter = $this->filter ?? 'today';
        Carbon::setLocale('es');
        $labels = [];
        $salidas = [];
        $noSalidas = [];

        switch ($filter) {
            case 'today':
                $dates = [Carbon::today()];
                break;

            case 'week':
                $dates = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i));
                break;

            case 'month':
                $dates = collect(range(0, Carbon::now()->daysInMonth - 1))->map(fn($i) => Carbon::now()->startOfMonth()->addDays($i));
                break;

            case 'year':
                $dates = collect(range(0, 11))->map(fn($i) => Carbon::now()->startOfYear()->addMonths($i));
                break;

            default:
                $dates = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i));
        }

        foreach ($dates as $fecha) {
            if ($filter === 'year') {
                $labels[] = $fecha->translatedFormat('F'); // Nombre del mes

                $salidas[] = SalidaDeBuses::whereMonth('fecha_salida', $fecha->month)
                    ->whereYear('fecha_salida', $fecha->year)
                    ->where('estado_salida', 'salida')
                    ->count();

                $noSalidas[] = SalidaDeBuses::whereMonth('fecha_salida', $fecha->month)
                    ->whereYear('fecha_salida', $fecha->year)
                    ->where('estado_salida', 'no_salida')
                    ->count();
            } else {
                $labels[] = $fecha->isoFormat('D [de] MMMM');

                $salidas[] = SalidaDeBuses::whereDate('fecha_salida', $fecha)
                    ->where('estado_salida', 'salida')
                    ->count();

                $noSalidas[] = SalidaDeBuses::whereDate('fecha_salida', $fecha)
                    ->where('estado_salida', 'no_salida')
                    ->count();
            }
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
    /* Filtros */
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Última semana',
            'month' => 'Último mes',
            'year' => 'Este año',
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
    /* Solo visible para usuarios con permiso */
    public static function canView(): bool
{
    return Gate::allows('widget_SalidaBusesChart');
}
}
