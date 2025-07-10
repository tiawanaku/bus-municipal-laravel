<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TiposMantenimientosPastel extends ChartWidget
{
    protected static ?string $heading = 'Tipos de Mantenimiento Total';

    protected function getData(): array
    {

        $tipos = DB::table('mantenimientos')
            ->select('tipo_mantenimiento', DB::raw('count(*) as total'))
            ->groupBy('tipo_mantenimiento')
            ->pluck('total', 'tipo_mantenimiento');

        return [
            'datasets' => [
                [
                    'data' => $tipos->values(),
                    'backgroundColor' => $this->generateColors($tipos->count()),
                ],
            ],
            'labels' => $tipos->keys(),
        ];
    }

    private function generateColors(int $count): array
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $hue = ($i * 360 / $count) % 360;
            $colors[] = "hsl($hue, 70%, 60%)";
        }
        return $colors;
    }

    protected function getType(): string
    {
        return 'pie';
    }
      /* Solo visible en la página de Unidad de Mantenimiento */
    public static function canView(): bool
    {
        return request()->routeIs('filament.pages.mantenimiento-dashboard');
    }
}
