<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TecnicoResource\Pages;
use App\Filament\Resources\TecnicoResource\RelationManagers;
use App\Models\Tecnico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TecnicoResource extends Resource
{
    protected static ?string $model = Tecnico::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('nombre')
                ->label('Nombre(s)')
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('apellido_paterno')
                ->label('Apellido Paterno')
                ->required()
                ->maxLength(255),
    
                Forms\Components\TextInput::make('apellido_materno')
                ->label('Apellido Materno')
                ->required()
                ->maxLength(255),
    
                Forms\Components\DatePicker::make('fecha_nacimiento')
                ->label('Fecha de Nacimiento')
                ->required(),
    
                Forms\Components\TextInput::make('numero_contrato')
                ->label('Número de Contrato')
                ->required()
                ->maxLength(255),
    
                Forms\Components\TextInput::make('numero_contacto')
                ->label('Número de Celular')
                ->required()
                ->maxLength(255),
    
                Forms\Components\TextInput::make('numero_referencia')
                ->label('Número de celular referencial')
                ->required()
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nombre')
                ->searchable(),
                Tables\Columns\TextColumn::make('apellido_paterno'),
                Tables\Columns\TextColumn::make('apellido_materno'),
                Tables\Columns\TextColumn::make('fecha_nacimiento'),
                Tables\Columns\TextColumn::make('numero_contacto'),
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
            'index' => Pages\ListTecnicos::route('/'),
            'create' => Pages\CreateTecnico::route('/create'),
            'edit' => Pages\EditTecnico::route('/{record}/edit'),
        ];
    }
}
