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

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Información Personal')
                ->schema([
                    Forms\Components\TextInput::make('nombre')->required()->label('Nombre'),
                    Forms\Components\TextInput::make('apellido_paterno')->required()->label('Apellido Paterno'),
                    Forms\Components\TextInput::make('apellido_materno')->required()->label('Apellido Materno'),
                    Forms\Components\TextInput::make('ci')->required()->label('CI'),
                    Forms\Components\TextInput::make('complemento')->label('Complemento')->nullable(),
                    Forms\Components\TextInput::make('ci_expedido')->required()->label('CI Expedido'),
                    Forms\Components\TextInput::make('celular')->required()->label('Celular'),
                    Forms\Components\Select::make('genero')
                        ->required()
                        ->options([
                            'masculino' => 'Masculino',
                            'femenino' => 'Femenino',
                            'otro' => 'Otro',
                        ])
                        ->label('Género'),
                ])
                ->columns(3),

            Section::make('Contrato')
                ->schema([
                    Forms\Components\TextInput::make('numero_contrato')->required()->label('Número de Contrato'),
                    Forms\Components\DatePicker::make('fecha_inicio_contrato')->label('Fecha de Inicio de Contrato')->required(),
                    Forms\Components\DatePicker::make('fecha_fin_contrato')->label('Fecha de Fin de Contrato')->required(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('apellido_paterno')->label('Apellido Paterno')->searchable(),
                Tables\Columns\TextColumn::make('apellido_materno')->label('Apellido Materno')->searchable(),
                Tables\Columns\TextColumn::make('ci')->label('CI'),
                Tables\Columns\BadgeColumn::make('genero')->label('Género')->colors([
                    'primary' => 'masculino',
                    'pink' => 'femenino',
                    'gray' => 'otro',
                ]),
                Tables\Columns\TextColumn::make('fecha_inicio_contrato')->label('Inicio Contrato'),
                Tables\Columns\TextColumn::make('fecha_fin_contrato')->label('Fin Contrato'),
            ])
            ->defaultSort('nombre')

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