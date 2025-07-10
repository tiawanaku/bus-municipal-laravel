<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Aviso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TiposAvisosChart extends ChartWidget
{
   protected static ?string $heading = 'Tipos de Avisos por Mes';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        Carbon::setLocale('es');
        $filter = $this->filter ?? 'today';

        $query = Aviso::select(
            DB::raw('YEAR(inicio_periodo) as year'),
            DB::raw('MONTH(inicio_periodo) as month'),
            'noticia',
            DB::raw('count(*) as total')
        )->whereNotNull('inicio_periodo');

        // Aplicar filtro de fecha
        switch ($filter) {
            case 'today':
                $query->whereDate('inicio_periodo', Carbon::today());
                break;

            case 'week':
                $query->whereBetween('inicio_periodo', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()]);
                break;

            case 'month':
                $query->whereMonth('inicio_periodo', Carbon::now()->month)
                    ->whereYear('inicio_periodo', Carbon::now()->year);
                break;

            case 'year':
                $query->whereYear('inicio_periodo', Carbon::now()->year);
                break;
        }

        $datos = $query->groupBy(DB::raw('YEAR(inicio_periodo)'), DB::raw('MONTH(inicio_periodo)'), 'noticia')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = $datos->map(function ($item) {
            return Carbon::createFromDate($item->year, $item->month)->translatedFormat('F Y');
        })->unique()->values();

        $noticias = $datos->pluck('noticia')->unique();

        $datosIndexados = $datos->mapWithKeys(function ($item) {
            $label = Carbon::createFromDate($item->year, $item->month)->translatedFormat('F Y');
            return [$item->noticia . '|' . $label => $item->total];
        });

        $datasets = $noticias->map(function ($noticia) use ($labels, $datosIndexados) {
            return [
                'label' => $noticia,
                'data' => $labels->map(function ($label) use ($noticia, $datosIndexados) {
                    return $datosIndexados[$noticia . '|' . $label] ?? 0;
                }),
                'borderColor' => $this->getColorForNoticia($noticia),
                'backgroundColor' => 'transparent', // para que el fondo no tape la línea
                'borderWidth' => 2,
                'fill' => false,
                'pointRadius' => 4,
                'pointHoverRadius' => 6,
            ];
        });

        return [
            'labels' => $labels,
            'datasets' => $datasets->toArray(),
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

    private function getColorForNoticia($noticia)
    {
        return match ($noticia) {
            'Cambio de Ruta' => '#6366F1',
            'Bloqueo de Vías' => '#EF4444',
            'Nueva Ruta' => '#10B981',
            'Suspención del servicio' => '#F59E0B',
            'Otro' => '#E879F9',
            default => '#6c757d',
        };
    }
    public static function canView(): bool
{
    return Gate::allows('widget_TiposAvisosChart');
}
}
