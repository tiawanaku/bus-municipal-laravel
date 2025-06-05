<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Widgets;

use App\Models\EntregaTalonario;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class Cajeros extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Preferenciales restantes', $this->getCantidadRestantePreferencial())
                ->description(
                    'Tickets preferenciales: ' . $this->getTotalBoletosPreferenciales()
                )
                ->icon('heroicon-o-ticket')
                ->iconColor('primary')
                ->descriptionIcon('heroicon-m-ticket', 'before')
                ->descriptionColor('primary')
                ->progress(
                    intval(
                        ($this->getCantidadPreferenciales() > 0)
                            ? ($this->getCantidadRestantePreferencial() / $this->getCantidadPreferenciales()) * 100
                            : 0
                    )
                )
                ->progressBarColor('primary'),

            Stat::make('Regulares restantes', $this->getCantidadRestanteRegular())
                ->description(
                    'Tickets regulares: ' . $this->getTotalBoletosRegulares()
                )
                ->icon('heroicon-o-ticket')
                ->iconColor('danger')
                ->descriptionIcon('heroicon-m-ticket', 'before')
                ->descriptionColor('danger')
                ->progress(
                    intval(
                        ($this->getCantidadRegulares() > 0)
                            ? ($this->getCantidadRestanteRegular() / $this->getCantidadRegulares()) * 100
                            : 0
                    )
                )
                ->progressBarColor('danger'),

            Stat::make('Total a Recaudar (Bs.)', $this->getRecaudacionTotal())
                ->description('Tickets preferen.:' . $this->getTotalBoletosPreferenciales() .
                    ' | Tickets regulares: ' . $this->getTotalBoletosRegulares())
                ->iconColor('warning')
                ->icon('heroicon-o-currency-dollar')
                ->descriptionIcon('heroicon-o-currency-dollar', 'before')
                ->descriptionColor('warning'),
        ];
    }

    // Preferenciales (solo registros con estado_preferencial = 1)

    private function getCantidadRestantePreferencial(): int
    {
        return EntregaTalonario::where('estado_preferencial', 1)->sum('cantidad_restante_preferencial') ?: 0;
    }

    private function getCantidadPreferenciales(): int
    {
        return EntregaTalonario::where('estado_preferencial', 1)->sum('cantidad_preferenciales') ?: 0;
    }

    private function getTotalBoletosPreferenciales(): int
    {
        return EntregaTalonario::where('estado_preferencial', 1)->sum('total_boletos_preferenciales') ?: 0;
    }

    // Regulares (solo registros con estado_regular = 1)

    private function getCantidadRestanteRegular(): int
    {
        return EntregaTalonario::where('estado_regular', 1)->sum('cantidad_restante_regular') ?: 0;
    }

    private function getCantidadRegulares(): int
    {
        return EntregaTalonario::where('estado_regular', 1)->sum('cantidad_regulares') ?: 0;
    }

    private function getTotalBoletosRegulares(): int
    {
        return EntregaTalonario::where('estado_regular', 1)->sum('total_boletos_regulares') ?: 0;
    }

    private function getRecaudacionTotal(): string
    {
        $totalPreferencial = EntregaTalonario::where('estado_preferencial', 1)->sum('total_aproximado_bolivianos_preferencial') ?: 0;
        $totalRegular = EntregaTalonario::where('estado_regular', 1)->sum('total_aproximado_bolivianos_regular') ?: 0;

        $total = $totalPreferencial + $totalRegular;

        return number_format($total, 2, ',', '.');
    }
}