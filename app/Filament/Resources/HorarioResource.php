<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioResource\Pages;
use App\Filament\Resources\HorarioResource\RelationManagers;
use App\Models\Horario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class HorarioResource extends Resource
{
    protected static ?string $model = Horario::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Administración Sitio Web';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('turno')
                ->required()
                ->label('Turno')
                ->maxLength(50),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->maxLength(255)
                ->rows(2),

            TimePicker::make('desde')
                ->required()
                ->label('Desde'),

            TimePicker::make('hasta')
                ->required()
                ->label('Hasta'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                   TextColumn::make('turno')->sortable()->searchable(),
            TextColumn::make('descripcion')->limit(50),
            TextColumn::make('desde'),
            TextColumn::make('hasta'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListHorarios::route('/'),
            'create' => Pages\CreateHorario::route('/create'),
            'edit' => Pages\EditHorario::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
