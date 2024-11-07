<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalidaDeBusesResource\Pages;
use App\Models\SalidaDeBuses;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Form; // Directly import the Form class
use Filament\Resources\Table;
use Filament\Tables;
use App\Models\Anfitrion;

class SalidaDeBusesResource extends Resource
{
    protected static ?string $model = SalidaDeBuses::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Gestión de Buses';

    public static function form(Forms\Form $form): Forms\Form // Return type should be Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('n_bus')
                    ->required(),
                Forms\Components\TextInput::make('n_ficha')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_salida')
                    ->required(),
                Forms\Components\TimePicker::make('hora_salida')
                    ->required(),
                Forms\Components\Select::make('id_anfitrion') // Usamos el alias aquí
                    ->label('Anfitrión')
                    ->options(Anfitrion::all()->mapWithKeys(function ($anfitrion) {
                        return [$anfitrion->id => $anfitrion->full_name]; // Utilizamos el atributo calculado
                    }))
                    ->required(),

                Forms\Components\Select::make('id_designacion_bus')
                    ->label('numero del bus')
                    ->relationship('designacionBus', 'id_buses') // Assuming 'n_bus' is a field in AsignacionDeBus
                    ->required(),

                Forms\Components\Select::make('estado_salida')
                    ->options([
                        'salida' => 'Salida',
                        'no_salida' => 'No Salida',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('motivo_no_salida')->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('n_bus'),
                Tables\Columns\TextColumn::make('n_ficha'),
                Tables\Columns\TextColumn::make('fecha_salida'),
                Tables\Columns\TextColumn::make('hora_salida'),
                Tables\Columns\TextColumn::make('anfitrion.name'), // Display the name of the anfitrion
                Tables\Columns\TextColumn::make('designacionBus.n_bus'), // Display the bus designation
                Tables\Columns\TextColumn::make('estado_salida'),
                Tables\Columns\TextColumn::make('motivo_no_salida'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalidaDeBuses::route('/'),
            'create' => Pages\CreateSalidaDeBuses::route('/create'),
            'edit' => Pages\EditSalidaDeBuses::route('/{record}/edit'),
        ];
    }
}
