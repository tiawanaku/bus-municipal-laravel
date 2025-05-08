<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConductorResource\Pages;
use App\Filament\Resources\ConductorResource\RelationManagers;
use App\Models\Conductor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConductorResource extends Resource
{
    protected static ?string $model = Conductor::class;

    //para cambiar el nombre de la etiqueta
    protected static ?string $navigationLabel = 'Conductores';

    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

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
                    ->minDate(now()->subYears(75))
                    ->maxDate(now()->subYears(18))
                    ->required(),

                Forms\Components\Select::make('tipo_licencia')
                    ->options([
                        'Categoria A' => 'Categoria A',
                        'Categoria B' => 'Categoria B',
                        'Categoria C' => 'Categoria C',
                    ])
                    ->label('Categoria')
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
            'index' => Pages\ListConductors::route('/'),
            'create' => Pages\CreateConductor::route('/create'),
            'edit' => Pages\EditConductor::route('/{record}/edit'),
        ];
    }
}
