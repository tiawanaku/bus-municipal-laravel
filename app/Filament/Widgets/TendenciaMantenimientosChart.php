<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\Mantenimiento;

class TendenciaMantenimientosChart extends ChartWidget
{
     protected static ?string $heading = 'Tendencias Mantenimiento';

    public ?string $filter = 'today'; // Agregamos la propiedad del filtro

    protected function getData(): array
{
    Carbon::setLocale('es');
    $filter = $this->filter ?? 'today';
    $startDate = null;
    $endDate = Carbon::today();

    switch ($filter) {
        case 'today':
            $startDate = Carbon::today();
            break;
        case 'week':
            $startDate = Carbon::today()->subDays(6);
            break;
        case 'month':
            $startDate = Carbon::now()->startOfMonth();
            break;
        case 'year':
            $startDate = Carbon::now()->startOfYear();
            break;
        default:
            $startDate = Carbon::today();
    }

    $tipos = Mantenimiento::select('tipo_mantenimiento')
        ->distinct()
        ->pluck('tipo_mantenimiento')
        ->toArray();

    $paletaColores = ['#0B4A9B', '#E4005F', '#F39C12', '#1abc9c', '#9b59b6', '#34495e', '#e67e22', '#2ecc71', '#e74c3c', '#3498db'];

    $labels = [];
    $datasets = [];

    if ($filter === 'year') {
        // Mostrar solo los meses
        for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
            $labels[] = ucfirst($date->translatedFormat('F')); // ejemplo: "Enero"
        }

        foreach ($tipos as $index => $tipo) {
            $data = [];
            $color = $paletaColores[$index % count($paletaColores)];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $count = Mantenimiento::whereBetween('fecha_mantenimiento', [$startOfMonth, $endOfMonth])
                    ->where('estado_mantenimiento', 'realizado')
                    ->where('tipo_mantenimiento', $tipo)
                    ->count();

                $data[] = $count;
            }

            $datasets[] = [
                'label' => $tipo,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $this->hexToRgba($color, 0.2),
                'fill' => true,
            ];
        }

    } else {
        // Mostrar días con nombre de mes completo
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $labels[] = $date->translatedFormat('d F'); // ejemplo: "01 junio"
        }

        foreach ($tipos as $index => $tipo) {
            $data = [];
            $color = $paletaColores[$index % count($paletaColores)];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $count = Mantenimiento::whereDate('fecha_mantenimiento', $date)
                    ->where('estado_mantenimiento', 'realizado')
                    ->where('tipo_mantenimiento', $tipo)
                    ->count();

                $data[] = $count;
            }

            $datasets[] = [
                'label' => $tipo,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $this->hexToRgba($color, 0.2),
                'fill' => true,
            ];
        }
    }

    return [
        'labels' => $labels,
        'datasets' => $datasets,
    ];
}


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

    protected function hexToRgba($color, $opacity = 1)
    {
        $color = str_replace('#', '', $color);
        if (strlen($color) == 6) {
            $hex = [
                hexdec(substr($color, 0, 2)),
                hexdec(substr($color, 2, 2)),
                hexdec(substr($color, 4, 2)),
            ];
            return "rgba({$hex[0]}, {$hex[1]}, {$hex[2]}, {$opacity})";
        }
        return $color;
    }

    public static function canView(): bool
    {
        return request()->routeIs('filament.pages.mantenimiento-dashboard');
    }
}
