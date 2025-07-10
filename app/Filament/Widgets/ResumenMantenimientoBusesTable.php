<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Bus;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\DateFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

class ResumenMantenimientoBusesTable extends BaseWidget
{
    protected static ?string $heading = 'Resumen Mantenimiento de Buses';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Bus::query()
                    ->withCount('mantenimientos') // total de mantenimientos
                    ->withMax('mantenimientos', 'fecha_mantenimiento') // último mantenimiento
            )
            ->columns([
                Tables\Columns\TextColumn::make('numero_bus')
                    ->label('Número Bus')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mantenimientos_count')
                    ->label('Total Mantenimientos')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mantenimientos_max_fecha_mantenimiento')
                    ->label('Último Mantenimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),


            ])
            ->filters([

            
                /* Filtro por fechas */
                Filter::make('rango_fecha')
                    ->form([
                        DatePicker::make('fecha_desde')->label('Desde'),
                        DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['fecha_desde']) || !empty($data['fecha_hasta'])) {
                            $query->where(function ($q) use ($data) {
                                $q->whereHas('mantenimientos', function ($q2) use ($data) {
                                    if (!empty($data['fecha_desde'])) {
                                        $q2->whereDate('fecha_mantenimiento', '>=', $data['fecha_desde']);
                                    }
                                    if (!empty($data['fecha_hasta'])) {
                                        $q2->whereDate('fecha_mantenimiento', '<=', $data['fecha_hasta']);
                                    }
                                })
                                    ->orWhereDoesntHave('mantenimientos'); // Incluye buses sin mantenimientos
                            });
                        }
                    }),

            ]);


    }
     /* Solo visible en la página de Unidad de Mantenimiento */
    public static function canView(): bool
    {
        return request()->routeIs('filament.pages.mantenimiento-dashboard');
    }
}
