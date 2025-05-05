<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioTalonariosResource\Pages;
use App\Filament\Resources\InventarioTalonariosResource\RelationManagers;
use App\Models\InventarioTalonarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventarioTalonariosResource extends Resource
{
    protected static ?string $model = InventarioTalonarios::class;

    protected static ?string $navigationLabel = 'Inventarios de Talonarios';
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Selección del cajero
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('cajero_id')
                            ->label('Encargado de Recepción de Talonarios')
                            ->options(function () {
                                return \App\Models\Cajero::all()->pluck('nombre', 'id')->mapWithKeys(function ($item, $key) {
                                    $cajero = \App\Models\Cajero::find($key);
                                    $fullName = $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                                    return [$key => $fullName];
                                });
                            })
                            ->required(),
                    ])
                    ->label('Datos del Cajero'),

                // Tarjeta para Preferenciales
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('cantidad_preferenciales')
                                    ->label('Cantidad Paquetes Preferenciales')
                                    ->required(),

                                Forms\Components\TextInput::make('rango_inicial_preferencial')
                                    ->label('Rango Inicial Preferencial')
                                    ->required(),

                                Forms\Components\TextInput::make('rango_final_preferencial')
                                    ->label('Rango Final Preferencial')
                                    ->required(),

                                Forms\Components\TextInput::make('cantidad_restante_preferencial')
                                    ->label('Cantidad Restante Preferencial')
                                    ->required(),
                            ]),
                    ])
                    ->label('Tarjetas Preferenciales'),

                // Tarjeta para Regulares
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('cantidad_regulares')
                                    ->label('Cantidad Paquetes Regulares')
                                    ->required(),

                                Forms\Components\TextInput::make('rango_inicial_regular')
                                    ->label('Rango Inicial Regular')
                                    ->required(),

                                Forms\Components\TextInput::make('rango_final_regular')
                                    ->label('Rango Final Regular')
                                    ->required(),

                                Forms\Components\TextInput::make('cantidad_restante_regular')
                                    ->label('Cantidad Restante Regular')
                                    ->required(),
                            ]),
                    ])
                    ->label('Tarjetas Regulares'),

                // Observaciones
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->nullable(),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cantidad_preferenciales'),
                Tables\Columns\TextColumn::make('rango_inicial_preferencial'),
                Tables\Columns\TextColumn::make('rango_final_preferencial'),
                Tables\Columns\TextColumn::make('cantidad_restante_preferencial'),
                Tables\Columns\TextColumn::make('cantidad_regulares'),
                Tables\Columns\TextColumn::make('rango_inicial_regular'),
                Tables\Columns\TextColumn::make('rango_final_regular'),
                Tables\Columns\TextColumn::make('cantidad_restante_regular'),
                Tables\Columns\TextColumn::make('observaciones'),
            ])
            ->filters([
                // Agrega filtros aquí si es necesario
            ])
            ->searchable() // Esta línea habilita la búsqueda
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarioTalonarios::route('/'),
            'create' => Pages\CreateInventarioTalonarios::route('/create'),
            'edit' => Pages\EditInventarioTalonarios::route('/{record}/edit'),
        ];
    }
}