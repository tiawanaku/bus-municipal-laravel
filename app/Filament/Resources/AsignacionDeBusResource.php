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
                Group::make()
                    ->schema([
                        FilamentSelect::make('id_conductor')
                            ->label('Conductor')
                            ->options(Conductor::all()->mapWithKeys(function ($conductor) {
                                return [$conductor->id => $conductor->nombre_completo];
                            }))
                            ->required()
                            ->placeholder('Selecciona un conductor'),

                        FilamentSelect::make('id_anfitrion')
                            ->label('Anfitrión')
                            ->options(
                                Anfitrion::whereNotNull('nombre')
                                    ->whereNotNull('apellido_paterno')
                                    ->whereNotNull('apellido_materno')
                                    ->get()
                                    ->mapWithKeys(function ($anfitrion) {
                                        return [$anfitrion->id => $anfitrion->full_name];
                                    })
                            )
                            ->required()
                            ->placeholder('Selecciona un anfitrión'),

                        FilamentSelect::make('id_buses')
                            ->label('Bus')
                            ->relationship('bus', 'numero_bus')
                            ->required()
                            ->placeholder('Selecciona un bus'),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'p-6 min-h-[250px] bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg border border-transparent text-white']), // Fondo degradado y bordes redondeados

                Group::make()
                    ->schema([
                        TextInput::make('n_ficha')
                            ->label('Número de Ficha')
                            ->numeric()
                            ->required()
                            ->placeholder('Ingrese el número de ficha'),

                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->nullable()
                            ->placeholder('Ingrese observaciones adicionales...'),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'p-6 min-h-[250px] bg-gradient-to-r from-green-500 to-green-700 rounded-xl shadow-lg border border-transparent text-white']), // Fondo degradado y bordes redondeados

                Group::make()
                    ->schema([
                        TimePicker::make('hora_salida')
                            ->label('Hora de Salida')
                            ->required()
                            ->placeholder('Selecciona la hora de salida'),

                        DatePicker::make('fecha_designacion')
                            ->label('Fecha de Designación')
                            ->required()
                            ->placeholder('Selecciona la fecha de designación'),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'p-6 min-h-[250px] bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl shadow-lg border border-transparent text-white']), // Fondo degradado y bordes redondeados
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

                TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->limit(50),

                TextColumn::make('fecha_designacion') // Usando DateTimeColumn
                    ->label('Fecha de Designación')
                    ->sortable(),
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