<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GaleriaResource\Pages;
use App\Filament\Resources\GaleriaResource\RelationManagers;
use App\Models\Galeria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class GaleriaResource extends Resource
{
    protected static ?string $model = Galeria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationGroup = 'Administración Sitio Web';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                FileUpload::make('imagen')
                ->image()
                ->required()
                ->directory('/img/galeria'),
            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3),
            Toggle::make('activo')
                ->label('Visible')
                ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('imagen')
                ->label('Imagen')
                ->circular(),
            TextColumn::make('descripcion')
                ->limit(30),
            
            IconColumn::make('activo')
                ->boolean()
                ->label('Visible'),
            TextColumn::make('created_at')
                ->dateTime('d M Y'),
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
            'index' => Pages\ListGalerias::route('/'),
            'create' => Pages\CreateGaleria::route('/create'),
            'edit' => Pages\EditGaleria::route('/{record}/edit'),
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
