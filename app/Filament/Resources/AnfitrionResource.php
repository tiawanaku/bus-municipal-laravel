<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnfitrionResource\Pages;
use App\Filament\Resources\AnfitrionResource\RelationManagers;
use App\Models\Anfitrion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\DateColumn;

class AnfitrionResource extends Resource
{
    protected static ?string $model = Anfitrion::class;

    //para cambiar el nombre de la etiqueta
    protected static ?string $navigationLabel = 'Anfitriones';

    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->label('Nombre'),

                                Forms\Components\TextInput::make('apellido_paterno')
                                    ->required()
                                    ->label('Apellido Paterno'),

                                Forms\Components\TextInput::make('apellido_materno')
                                    ->required()
                                    ->label('Apellido Materno'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('ci')
                                    ->required()
                                    ->label('CI'),

                                Forms\Components\TextInput::make('complemento')
                                    ->nullable()
                                    ->label('Complemento'),

                                Forms\Components\TextInput::make('ci_expedido')
                                    ->required()
                                    ->label('CI Expedido'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('celular')
                                    ->required()
                                    ->label('Celular'),

                                Forms\Components\Select::make('genero')
                                    ->required()
                                    ->options([
                                        'masculino' => 'Masculino',
                                        'femenino' => 'Femenino',
                                        'otro' => 'Otro',
                                    ])
                                    ->label('Género'),

                                Forms\Components\TextInput::make('numero_contrato')
                                    ->required()
                                    ->label('Número de Contrato'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_inicio_contrato')
                                    ->required()
                                    ->label('Fecha de Inicio de Contrato'),

                                Forms\Components\DatePicker::make('fecha_fin_contrato')
                                    ->required()
                                    ->label('Fecha de Fin de Contrato'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre'),

                Tables\Columns\TextColumn::make('apellido_paterno')
                    ->label('Apellido Paterno'),

                Tables\Columns\TextColumn::make('apellido_materno')
                    ->label('Apellido Materno'),

                Tables\Columns\TextColumn::make('ci')
                    ->label('CI'),

                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular'),

                Tables\Columns\TextColumn::make('numero_contrato')
                    ->label('Número de Contrato'),

                Tables\Columns\TextColumn::make('fecha_inicio_contrato')
                    ->label('Fecha de Inicio')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_fin_contrato')
                    ->label('Fecha de Fin'),
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
            'index' => Pages\ListAnfitrions::route('/'),
            'create' => Pages\CreateAnfitrion::route('/create'),
            'edit' => Pages\EditAnfitrion::route('/{record}/edit'),
        ];
    }
}