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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Información del Responsable')
                    ->schema([
                        Forms\Components\Select::make('cajero_id')
                            ->label('Cajera Responsable')
                            ->options(function () {
                                return \App\Models\Cajero::all()
                                    ->pluck('full_name', 'id');
                            })
                            ->required()
                            ->columnSpan(2), // Ocupa dos columnas para mayor espacio
                    ])
                    ->columns(2), // Ajustar a dos columnas para mayor espacio

                Forms\Components\Fieldset::make('Detalles del Talonario')
                    ->schema([
                        Forms\Components\TextInput::make('codigo_autorizacion')
                            ->label('Código de Autorización')
                            ->required()
                            ->columnSpan(1), // Una columna

                        Forms\Components\Select::make('tipo_talonario')
                            ->label('Tipo de Talonario')
                            ->options([
                                'preferencial' => 'Preferencial',
                                'regular' => 'Regular',
                            ])
                            ->required()
                            ->columnSpan(1), // Una columna

                        Forms\Components\TextInput::make('cantidad_tickets')
                            ->label('Cantidad de Tickets')
                            ->numeric()
                            ->required()
                            ->columnSpan(1), // Una columna

                        Forms\Components\TextInput::make('numero_bloques')
                            ->label('Número de Bloques')
                            ->numeric()
                            ->required()
                            ->columnSpan(1), // Una columna
                    ])
                    ->columns(2), // Ajustar a dos columnas para mayor claridad

                Forms\Components\Fieldset::make('Rango y Valor')
                    ->schema([
                        Forms\Components\TextInput::make('rango_inicial')
                            ->label('Rango Inicial')
                            ->numeric()
                            ->required()
                            ->columnSpan(1), // Una columna

                        Forms\Components\TextInput::make('rango_final')
                            ->label('Rango Final')
                            ->numeric()
                            ->required()
                            ->columnSpan(1), // Una columna

                        Forms\Components\TextInput::make('valor_ticket_bs')
                            ->label('Valor de Ticket (Bs)')
                            ->numeric()
                            ->required()
                            ->columnSpan(2), // Ocupa más espacio por ser importante
                    ])
                    ->columns(2), // Ajustar a dos columnas para mayor claridad
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cajero.full_name')
                    ->label('Cajera Responsable'),
                Tables\Columns\TextColumn::make('codigo_autorizacion')
                    ->label('N° Autorización'),
                Tables\Columns\TextColumn::make('cantidad_tickets')
                    ->label('Tickets Disponibles'),
                Tables\Columns\TextColumn::make('rango_inicial')
                    ->label('Rango Inicial'),
                Tables\Columns\TextColumn::make('rango_final')
                    ->label('Rango Final'),
                Tables\Columns\TextColumn::make('numero_bloques')
                    ->label('N° Bloques'),
                Tables\Columns\TextColumn::make('valor_ticket_bs')
                    ->label('Valor Ticket (Bs)'),
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