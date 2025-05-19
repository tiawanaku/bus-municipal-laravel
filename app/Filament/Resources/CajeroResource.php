<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CajeroResource\Pages;
use App\Filament\Resources\CajeroResource\RelationManagers;
use App\Models\Cajero;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class CajeroResource extends Resource
{
    protected static ?string $model = Cajero::class;

    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo_cajero')
                    ->label('Tipo de Cajero')
                    ->options([
                        'principal' => 'Principal',
                        'secundario' => 'Secundario',
                    ])
                    ->required(),

                Forms\Components\Select::make('cajero_padre_id')
                    ->label('Cajero Padre (si es secundario)')
                    ->relationship('cajeroPadre', 'nombre')
                    ->searchable()
                    ->preload()
                    ->visible(fn($get) => $get('tipo_cajero') === 'secundario')
                    ->nullable(),

                Forms\Components\TextInput::make('nombre')->required(),
                Forms\Components\TextInput::make('apellido_paterno')->required(),
                Forms\Components\TextInput::make('apellido_materno')->required(),

                Forms\Components\TextInput::make('ci')->label('CI')->required(),
                Forms\Components\TextInput::make('complemento')->nullable(),
                Forms\Components\TextInput::make('ci_expedido')->label('CI Expedido')->required(),
                Forms\Components\TextInput::make('celular')->required(),
                Forms\Components\Select::make('genero')
                    ->options([
                        'masculino' => 'Masculino',
                        'femenino' => 'Femenino',
                        'otro' => 'Otro',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('numero_contrato')->required(),
                Forms\Components\DatePicker::make('fecha_inicio_contrato')->nullable(),
                Forms\Components\DatePicker::make('fecha_fin_contrato')->nullable(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable(),
                Tables\Columns\TextColumn::make('apellido_paterno'),
                Tables\Columns\TextColumn::make('apellido_materno'),
                Tables\Columns\TextColumn::make('tipo_cajero')->badge(),
                Tables\Columns\TextColumn::make('cajeroPadre.nombre')->label('Cajero Padre'),
                Tables\Columns\TextColumn::make('ci'),
                Tables\Columns\TextColumn::make('celular'),
                Tables\Columns\TextColumn::make('numero_contrato'),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCajeros::route('/'),
            'create' => Pages\CreateCajero::route('/create'),
            'edit' => Pages\EditCajero::route('/{record}/edit'),
        ];
    }
}