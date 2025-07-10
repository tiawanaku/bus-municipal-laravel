<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Bus;
use Carbon\Carbon;
use App\Models\SalidaDeBuses;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

class ResumenKilometrajeBusesTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Resumen de Salida Registradas de Buses';
    protected function getTableQuery(): Builder
    {
        return SalidaDeBuses::query()
            ->join('asignacion_de_bus', 'salida_de_buses.designacion_id', '=', 'asignacion_de_bus.id_designacion_bus')
            ->join('buses', 'asignacion_de_bus.id_buses', '=', 'buses.id')
            ->selectRaw('buses.id as bus_id, buses.numero_bus, SUM(kilometraje_llegada - kilometraje_salida) as km_recorridos')
            ->where('estado_salida', 'salida')
            ->whereNotNull('kilometraje_llegada')
            ->whereNotNull('kilometraje_salida')
            ->groupBy('buses.id', 'buses.numero_bus');
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('fecha_salida')
                ->form([
                    DatePicker::make('desde')->label('Desde'),
                    DatePicker::make('hasta')->label('Hasta'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    // Si no hay filtro, NO filtrar por fecha (mostrar todo)
                    if (empty($data['desde']) && empty($data['hasta'])) {
                        return $query;
                    }

                    // Aplicar filtro de rango si existe
                    return $query->where(function ($q) use ($data) {
                        if (!empty($data['desde'])) {
                            $q->whereDate('fecha_salida', '>=', $data['desde']);
                        }
                        if (!empty($data['hasta'])) {
                            $q->whereDate('fecha_salida', '<=', $data['hasta']);
                        }
                    });
                })
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('numero_bus')
                ->label('N° Bus')
                ->sortable(),

            TextColumn::make('km_recorridos')
                ->label('Km Recorridos')
                ->sortable()
                ->formatStateUsing(fn($state) => number_format($state ?? 0, 2) . ' km'),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return null;
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return null;
    }

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model $record): string
    {
        return (string) $record->designacion_id;
    }
    /* Solo visible en la página de Unidad de Operaciones */
    public static function canView(): bool
    {
        return request()->routeIs('filament.pages.operaciones-dashboard');
    }
}
