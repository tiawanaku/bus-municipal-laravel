<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Aviso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TiposAvisosChart extends ChartWidget
{
    protected static ?string $heading = 'Tipos de Avisos por Mes';
    protected static ?int $sort = 4;
/* Función para los Avisos clasificarlos en meses, y el tipo de noticia */
    protected function getData(): array
    {
        Carbon::setLocale('es');


        $datos = Aviso::select(
            DB::raw('YEAR(inicio_periodo) as year'),
            DB::raw('MONTH(inicio_periodo) as month'),
            'noticia',
            DB::raw('count(*) as total')
        )
            ->whereNotNull('inicio_periodo')
            ->groupBy(DB::raw('YEAR(inicio_periodo)'), DB::raw('MONTH(inicio_periodo)'), 'noticia')
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
                'backgroundColor' => $this->getColorForNoticia($noticia),
                'borderWidth' => 2,
                'fill' => false,
            ];
        });

        return [
            'labels' => $labels,
            'datasets' => $datasets->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    /* Función para cambiar el color en base a la noticia */
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
}
