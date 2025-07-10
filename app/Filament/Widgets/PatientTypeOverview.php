<?php

namespace App\Filament\Widgets;

use App\Models\Conductor;
use App\Models\Tecnico;
use App\Models\Cajero;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Bus;
use Illuminate\Support\Facades\Gate;


class PatientTypeOverview extends BaseWidget
{
     protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            //
            Card::make('Buses', Bus::query()->count()),
            Card::make('Conductores', Conductor::query()->count()),
            Card::make('Cajeros', Cajero::query()->count()),
            Card::make('Tecnicos', Tecnico ::query()->count()),
        ];
    }
     /* Solo visible para usuarios con permiso */
    public static function canView(): bool
{
    return Gate::allows('widget_PatientTypeOverview');
}
}
