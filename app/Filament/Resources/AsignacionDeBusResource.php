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

use App\Models\Conductor;


class AsignacionDeBusResource extends Resource
{
    protected static ?string $model = AsignacionDeBus::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck'; // Usa otro ícono como ejemplo


    protected static ?string $navigationLabel = 'Asignación de Buses';
    protected static ?string $pluralModelLabel = 'Asignaciones de Bus';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([

            FilamentSelect::make('id_conductor') // Usamos el alias aquí
                ->label('Conductor')
                ->options(Conductor::all()->mapWithKeys(function ($conductor) {
                    return [$conductor->id => $conductor->nombre_completo];
                }))
                ->required(),

            Select::make('id_buses')
                ->label('Bus')
                ->relationship('bus', 'numero_bus')
                ->required(),

            Textarea::make('observaciones')
                ->label('Observaciones')
                ->rows(3)
                ->nullable(),

            DatePicker::make('fecha_designacion')
                ->label('Fecha de Designación')
                ->required(),
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
