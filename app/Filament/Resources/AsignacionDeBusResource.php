<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignacionDeBusResource\Pages;
use App\Models\AsignacionDeBus;
use Filament\Forms;
use Filament\Forms\Form; // Asegúrate de usar este espacio de nombres
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\DateTimeColumn; // Cambiado a DateTimeColumn
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select as FilamentSelect; // Cambiamos el alias aquí


use Filament\Forms\Components\TimePicker;
use App\Models\Conductor;
use App\Models\Anfitrion;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Tables\Columns\IconColumn;
use App\Models\Bus;

use Filament\Forms\Components\Container; // Asegúrate de que esta línea esté presente


class AsignacionDeBusResource extends Resource
{
    protected static ?string $model = AsignacionDeBus::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck'; // Usa otro ícono como ejemplo


    protected static ?string $navigationLabel = 'Asignación de Buses';
    protected static ?string $pluralModelLabel = 'Asignaciones de Bus';

 public static function form(Forms\Form $form): Forms\Form
{
    return $form->schema([
        Grid::make()->columns(3)->schema([
            // SECCIÓN 2: Datos de Ficha y Fechas
            Group::make()
                ->schema([
                    Grid::make()->columns(2)->schema([
                        TextInput::make('n_ficha')
                            ->label('Número de Ficha')
                            ->numeric()
                            ->required()
                            ->placeholder('Ingrese el número de ficha')
                            ->columnSpan(1),

                        TimePicker::make('hora_salida')
                            ->label('Hora de Salida')
                            ->required()
                            ->placeholder('Selecciona la hora de salida')
                            ->columnSpan(1),

                        DatePicker::make('fecha_designacion')
                            ->label('Fecha de Designación')
                            ->required()
                            ->placeholder('Selecciona la fecha de designación')
                            ->columnSpan(1),

                        DatePicker::make('fin_asignacion')
                            ->label('Fecha Fin de Asignación')
                            ->reactive()
                            ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                $set('id_conductor', null);
                                $set('id_anfitrion', null);
                                $set('id_buses', null);
                            })
                            ->columnSpan(1),
                    ]),
                ])
                ->columnSpan(2)
                ->extraAttributes([
                    'class' => 'p-6 min-h-[300px] bg-green-50 dark:bg-green-900/20 rounded-xl shadow-lg border-2 border-green-300 dark:border-green-600'
                ]),

            // SECCIÓN 3: Observaciones y Estado
            Group::make()
                ->schema([
                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(4)
                        ->nullable()
                        ->placeholder('Ingrese observaciones adicionales...'),

                    Toggle::make('asignacion_activa')
                        ->label('Asignación activa')
                        ->default(true)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            // Setea la fecha si se desactiva, la limpia si se activa
                            if (!$state) {
                                $set('fin_asignacion', now()->toDateString());
                            } else {
                                $set('fin_asignacion', null);
                            }
                        }),
                ])
                ->columnSpan(1)
                ->extraAttributes([
                    'class' => 'p-6 min-h-[300px] bg-purple-50 dark:bg-purple-900/20 rounded-xl shadow-lg border-2 border-purple-300 dark:border-purple-600'
                ]),

            // SECCIÓN 1: Selección de Conductor, Anfitrión y Bus (FULL WIDTH)
            Group::make()
                ->schema([
                    Grid::make()->columns(3)->schema([
                        FilamentSelect::make('id_conductor')
                            ->label('Conductor')
                            ->options(function (callable $get, $livewire) {
                                $fecha = $get('fecha_designacion');
                                if (!$fecha)
                                    return [];

                                $ocupados = \App\Models\AsignacionDeBus::whereDate('fecha_designacion', '<=', $fecha)
                                    ->where(function ($query) use ($fecha) {
                                        $query->whereNull('fin_asignacion')
                                            ->orWhereDate('fin_asignacion', '>=', $fecha);
                                    });

                                if ($record = $livewire->record ?? null) {
                                    $ocupados->where('id_designacion_bus', '!=', $record->id_designacion_bus);
                                }

                                $idsOcupados = $ocupados->pluck('id_conductor');

                                return \App\Models\Conductor::whereNotIn('id', $idsOcupados)
                                    ->get()
                                    ->mapWithKeys(fn($c) => [
                                        $c->id => "{$c->nombre} {$c->apellido_paterno} {$c->apellido_materno}"
                                    ]);
                            })
                            ->reactive()
                            ->required()
                            ->placeholder('Selecciona un conductor')
                            ->columnSpan(1),

                        FilamentSelect::make('id_anfitrion')
                            ->label('Anfitrión')
                            ->options(function (callable $get, $livewire) {
                                $fecha = $get('fecha_designacion');
                                if (!$fecha)
                                    return [];

                                $ocupados = \App\Models\AsignacionDeBus::whereDate('fecha_designacion', '<=', $fecha)
                                    ->where(function ($query) use ($fecha) {
                                        $query->whereNull('fin_asignacion')
                                            ->orWhereDate('fin_asignacion', '>=', $fecha);
                                    });

                                if ($record = $livewire->record ?? null) {
                                    $ocupados->where('id_designacion_bus', '!=', $record->id_designacion_bus);
                                }

                                $idsOcupados = $ocupados->pluck('id_anfitrion');

                                return \App\Models\Anfitrion::whereNotIn('id', $idsOcupados)
                                    ->get()
                                    ->mapWithKeys(fn($a) => [
                                        $a->id => "{$a->nombre} {$a->apellido_paterno} {$a->apellido_materno}"
                                    ]);
                            })
                            ->reactive()
                            ->required()
                            ->placeholder('Selecciona un anfitrión')
                            ->columnSpan(1),

                        FilamentSelect::make('id_buses')
                            ->label('Bus')
                            ->options(function (callable $get, $livewire) {
                                $fecha = $get('fecha_designacion');
                                if (!$fecha)
                                    return [];

                                $ocupados = \App\Models\AsignacionDeBus::whereDate('fecha_designacion', '<=', $fecha)
                                    ->where(function ($query) use ($fecha) {
                                        $query->whereNull('fin_asignacion')
                                            ->orWhereDate('fin_asignacion', '>=', $fecha);
                                    });

                                if ($record = $livewire->record ?? null) {
                                    $ocupados->where('id_designacion_bus', '!=', $record->id_designacion_bus);
                                }

                                $idsOcupados = $ocupados->pluck('id_buses');

                                return \App\Models\Bus::whereNotIn('id', $idsOcupados)
                                    ->pluck('numero_bus', 'id');
                            })
                            ->reactive()
                            ->required()
                            ->placeholder('Selecciona un bus')
                            ->columnSpan(1),
                    ]),
                ])
                ->columnSpan(3) // Full width
                ->extraAttributes([
                    'class' => 'p-6 min-h-[200px] bg-blue-50 dark:bg-blue-900/20 rounded-xl shadow-lg border-2 border-blue-300 dark:border-blue-600 mb-6'
                ]),
        ]),
    ]);
}
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('conductor.nombre_completo') // Usa el método que devuelve el nombre completo
                    ->label('Conductor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bus.numero_bus')
                    ->label('Bus')
                    ->sortable()
                    ->searchable(),

               IconColumn::make('asignacion_activa')
    ->label('Asignación activa')
    ->boolean()
    ->getStateUsing(function($record) {
        // Si no hay fecha de fin O la fecha de fin es futura = ACTIVA
        if (is_null($record->fin_asignacion)) {
            return true; // Activa porque no tiene fecha de fin
        }
        
        // Si tiene fecha de fin, comparar con hoy
        $fechaFin = \Carbon\Carbon::parse($record->fin_asignacion);
        $hoy = \Carbon\Carbon::today();
        
        return $fechaFin->greaterThanOrEqualTo($hoy); // Activa si la fecha de fin es hoy o futura
    })
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('success')
    ->falseColor('danger'),

                TextColumn::make('fecha_designacion') // Usando DateTimeColumn
                    ->label('Fecha de Designación')
                    ->sortable(),

                TextColumn::make('fin_asignacion')
                    ->label('Fecha Fin Designación')
                    ->date('Y-m-d')
                    ->sortable(),

                    TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->limit(50),
            ])
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsignacionDeBuses::route('/'),
            'create' => Pages\CreateAsignacionDeBus::route('/create'),
            'edit' => Pages\EditAsignacionDeBus::route('/{record}/edit'),
        ];
    }
}