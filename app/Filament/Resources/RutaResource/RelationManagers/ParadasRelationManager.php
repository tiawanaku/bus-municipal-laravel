<?php

namespace App\Filament\Resources\RutaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Parada;


class ParadasRelationManager extends RelationManager
{
    protected static string $relationship = 'paradas';
    

    
    protected static ?string $inverseRelationship = 'ruta';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('id_paradas')  
                ->label('Parada')
                ->options(Parada::all()->pluck('nombre_parada,id_paradas'))  
                ->searchable(),
        ]);
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_parada'),
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make(), 
            ])
            ->actions([
                Tables\Actions\DissociateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
            ]);
    }
}
