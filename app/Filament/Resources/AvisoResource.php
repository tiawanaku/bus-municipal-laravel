<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvisoResource\Pages;
use App\Filament\Resources\AvisoResource\RelationManagers;
use App\Models\Aviso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvisoResource extends Resource
{
    protected static ?string $model = Aviso::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('noticia')
                ->label('Noticia')
                ->options([
                    'cambio_de_ruta' => 'Cambio de Ruta',
                    'bloqueo_de_vias' => 'Bloqueo de Vías',
                    'nueva_ruta' => 'Nueva Ruta',
                    'otro' => 'Otro',
                ])
                ->placeholder('Selecciona una noticia')
                ->required(),
                
            Forms\Components\DateTimePicker::make('inicio_periodo')
                ->label('Inicio de Periodo')
                ->default(now())
                ->required(),
                
            Forms\Components\DateTimePicker::make('fin_periodo')
                ->label('Fin de Periodo')
                ->default(now())
                ->nullable(),
                
            Forms\Components\Select::make('razon')
            ->label('Razón')
            ->required()
            ->options([
                'Cierre de vias' => 'Cierre de Vías',
                'Accidente' => 'Accidente',
                'Mantenimiento' => 'Mantenimiento',
                'Otro' => 'Otro',
            ])
            ->placeholder('Selecciona la razón'),
                
            Forms\Components\Textarea::make('paradas_afectadas')
                ->label('Paradas Afectadas')
                ->required(),
                
            Forms\Components\Textarea::make('detalle')
                ->label('Detalle')
                ->required()
                ->default("Estimados usuarios:\nDebido a ...\nAnte cualquier consulta comuníquese con nosotros a través de los siguientes números:"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('noticia'),
                Tables\Columns\TextColumn::make('inicio_periodo'),
                Tables\Columns\TextColumn::make('fin_periodo'),
                Tables\Columns\TextColumn::make('razon'),
                Tables\Columns\TextColumn::make('user.name') 
                ->label('Nombre del Usuario'),
                
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
            'index' => Pages\ListAvisos::route('/'),
            'create' => Pages\CreateAviso::route('/create'),
            'edit' => Pages\EditAviso::route('/{record}/edit'),
        ];
    }
}
