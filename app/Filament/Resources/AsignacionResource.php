<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignacionResource\Pages;
use App\Filament\Resources\AsignacionResource\RelationManagers;
use App\Models\Asignacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsignacionResource extends Resource
{
    protected static ?string $model = Asignacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    Forms\Components\BelongsToSelect::make('bus_id')
                        ->label('Bus')
                        ->relationship('bus', 'numero_placa')
                        ->required(),
    
                    Forms\Components\BelongsToSelect::make('conductor_id')
                        ->label('Conductor')
                        ->relationship('conductor', 'nombre')
                        ->required(),
    
                    Forms\Components\BelongsToSelect::make('anfitrion_id')
                        ->label('Anfitrion')
                        ->relationship('anfitrion', 'nombre')
                        ->required(),
    
                    Forms\Components\BelongsToSelect::make('ruta_id')
                        ->label('Ruta')
                        ->relationship('ruta', 'nombre')
                        ->required(),
    
                    Forms\Components\DateTimePicker::make('horario')
                        ->label('Horario')
                        ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('bus.numero_placa')
                ->searchable(),
                Tables\Columns\TextColumn::make('conductor.nombre'),
                Tables\Columns\TextColumn::make('anfitrion.nombre'),
                Tables\Columns\TextColumn::make('ruta.nombre'),
                Tables\Columns\TextColumn::make('horario'),
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
            'index' => Pages\ListAsignacions::route('/'),
            'create' => Pages\CreateAsignacion::route('/create'),
            'edit' => Pages\EditAsignacion::route('/{record}/edit'),
        ];
    }
}
