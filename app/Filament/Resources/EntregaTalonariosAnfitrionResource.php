<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;
use App\Filament\Resources\EntregaTalonariosAnfitrionResource\RelationManagers;
use App\Models\EntregaTalonariosAnfitrion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntregaTalonariosAnfitrionResource extends Resource
{
    protected static ?string $model = EntregaTalonariosAnfitrion::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestión de Talonarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de Entrega')
                    ->columns(3)
                    ->schema([

                        Forms\Components\Select::make('cajero_id')
                            ->label('Cajero')
                            ->relationship('cajero', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->nombre . ' ' . $record->apellido_paterno . ' ' . $record->apellido_materno)
                            ->required(),


                        Forms\Components\Select::make('anfitrion_id')
                            ->label('Anfitrión')
                            ->relationship('anfitrion', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->nombre . ' ' . $record->apellido_paterno . ' ' . $record->apellido_materno)
                            ->required(),


                        Forms\Components\TextInput::make('numero_autorizacion')
                            ->label('Número de Autorización')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Preferenciales')->schema([
                    Forms\Components\TextInput::make('cantidad_talonarios_preferenciales')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_inicial_preferenciales')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_final_preferenciales')->numeric()->required(),
                ])->columns(3),

                Forms\Components\Section::make('Regulares')->schema([
                    Forms\Components\TextInput::make('cantidad_talonarios_regulares')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_inicial_regulares')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_final_regulares')->numeric()->required(),

                ])->columns(3),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anfitrion.nombre')->label('Anfitrión')->searchable(),
                Tables\Columns\TextColumn::make('cajero.nombre')->label('Cajero')->searchable(),
                Tables\Columns\TextColumn::make('numero_autorizacion'),
                Tables\Columns\TextColumn::make('total_recaudar')->money('BOB'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListEntregaTalonariosAnfitrions::route('/'),
            'create' => Pages\CreateEntregaTalonariosAnfitrion::route('/create'),
            'edit' => Pages\EditEntregaTalonariosAnfitrion::route('/{record}/edit'),
        ];
    }
}