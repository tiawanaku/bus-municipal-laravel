<?php

namespace App\Filament\Widgets;

use App\Models\Tecnico;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\DateFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;

class RendimientoTecnicosTable extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            // Consultamos los técnicos y cuantos mantenimientos tienen
            ->query(
                Tecnico::query()
                    ->withCount('mantenimientos')
                    ->withCount([
                        'mantenimientos as realizados_count' => fn($query) => $query->where('estado_mantenimiento', 'realizado'),
                        'mantenimientos as pendientes_count' => fn($query) => $query->where('estado_mantenimiento', 'pendiente'),
                        'mantenimientos as en_proceso_count' => fn($query) => $query->where('estado_mantenimiento', 'en_proceso'),
                    ])// cuenta la cantidad de mantenimientos

            )
            ->columns([
                TextColumn::make('nombre')
                    ->label('Técnico'),

                TextColumn::make('nombre')->label('Técnico'),
                TextColumn::make('realizados_count')->label('Realizados')->toggleable(),
                TextColumn::make('pendientes_count')->label('Pendientes')->toggleable(),
                TextColumn::make('en_proceso_count')->label('En Proceso')->toggleable(),
                TextColumn::make('mantenimientos_count')
                    ->label('Total Mantenimientos')
                    ->sortable()
                    ->toggleable(),

            ])
            ->filters([

                /* Filtrar por estado */
                SelectFilter::make('estado_mantenimiento')
                    ->label('Estado del mantenimiento')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'realizado' => 'Realizado',
                        'en_proceso' => 'En proceso',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('mantenimientos', function ($q) use ($data) {
                                $q->where('estado_mantenimiento', $data['value']);
                            });
                        }
                    }),
                /* Filtro por fechas */
                Filter::make('rango_fecha')
                    ->form([
                        DatePicker::make('fecha_desde')->label('Desde'),
                        DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query->whereHas('mantenimientos', function ($q) use ($data) {
                            if (!empty($data['fecha_desde'])) {
                                $q->whereDate('fecha_mantenimiento', '>=', $data['fecha_desde']);
                            }
                            if (!empty($data['fecha_hasta'])) {
                                $q->whereDate('fecha_mantenimiento', '<=', $data['fecha_hasta']);
                            }
                        });
                    }),
            ]);
    }
    public static function canView(): bool
    {
        return request()->routeIs('filament.pages.mantenimiento-dashboard');
    }
}
