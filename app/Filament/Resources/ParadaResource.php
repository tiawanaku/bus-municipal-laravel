<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParadaResource\Pages;
use App\Filament\Resources\ParadaResource\RelationManagers;
use App\Models\Parada;
use Filament\Forms;
use Filament\Forms\Form;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use App\Forms\Components\LeafletMap;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\Hidden;

class ParadaResource extends Resource
{
    protected static ?string $model = Parada::class;

    protected static ?string $navigationGroup = 'Administrador del Sistema';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        
        ->schema([
            LeafletMap::make('lat_long')
                ->extraAttributes(['data-point-field' => 'lat_long']),

                Forms\Components\TextInput::make('nombre_parada')
                ->label('Nombre(s)')
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('sentido')
                ->label('Sentido(s)')
                ->required()
                ->maxLength(255),
        ]);
       
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_parada')
                ->label('Nombre de la Parada')
                ->sortable()
                ->searchable(),

            TextColumn::make('ruta.nombre')->label('Ruta')->sortable(), 


            TextColumn::make('sentido')
                ->label('Sentido')
                ->sortable()
                ->searchable(),
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
            'index' => Pages\ListParadas::route('/'),
            'create' => Pages\CreateParada::route('/create'),
            'edit' => Pages\EditParada::route('/{record}/edit'),
        ];
    }
}
