<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaTalonarioResource\Pages;
use App\Filament\Resources\EntregaTalonarioResource\RelationManagers;
use App\Models\EntregaTalonario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class EntregaTalonarioResource extends Resource
{
    protected static ?string $model = EntregaTalonario::class;

    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('responsable_entrega')
                    ->required()
                    ->label('Responsable de la Entrega'),

                Forms\Components\Select::make('cajero_id')
                    ->relationship('cajero', 'nombre') // Relación con cajero
                    ->required()
                    ->searchable() // Permite la búsqueda
                    ->label('Cajero'),

                Forms\Components\TextInput::make('numero_paquetes_entregados')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->label('Número de Paquetes Entregados'),

                Forms\Components\TextInput::make('cantidad_talonarios')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->label('Cantidad de Talonarios'),

                Forms\Components\TextInput::make('cantidad_tickets')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->label('Cantidad de Tickets'),

                Forms\Components\DatePicker::make('fecha_entrega')
                    ->required()
                    ->label('Fecha de Entrega'),

                Forms\Components\Select::make('tipo_talonarios')
                    ->options([
                        'regular' => 'Regular',
                        'preferencial' => 'Preferencial',
                    ])
                    ->required()
                    ->label('Tipo de Talonarios'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListEntregaTalonarios::route('/'),
            'create' => Pages\CreateEntregaTalonario::route('/create'),
            'edit' => Pages\EditEntregaTalonario::route('/{record}/edit'),
        ];
    }
}

