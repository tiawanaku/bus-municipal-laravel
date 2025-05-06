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
                                    ->label('Cantidad Talonarios Preferenciales')
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
                                    ->label('Cantidad Talonarios Regulares')
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
                Tables\Columns\TextColumn::make('cajero_id')
                    ->label('Cajero')
                    ->getStateUsing(function ($record) {
                        $cajero = \App\Models\Cajero::find($record->cajero_id);
                        return $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                    }),
                Tables\Columns\TextColumn::make('cantidad_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('rango_inicial_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_final_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_restante_preferencial')
                    ->label('Preferenciales Restantes')
                    ->badge() // opcional: muestra como etiqueta de color
                    ->color(function ($state) {
                        if ($state < 800) {
                            return 'danger'; // rojo
                        } elseif ($state < 1200) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos')
                    ->label('Total a Recaudar Pref. ')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_regulares')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_inicial_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_final_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_restante_regular')
                    ->label('Regulares Restantes')
                    ->badge()
                    ->color(function ($state) {
                        if ($state < 800) {
                            return 'danger'; // rojo
                        } elseif ($state < 1200) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_regulares')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_regular')
                    ->label('Total a Recaudar Regular')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('observaciones')
                    ->toggleable(isToggledHiddenByDefault: true),
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