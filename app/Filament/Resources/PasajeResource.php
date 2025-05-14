<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PasajeResource\Pages;
use App\Filament\Resources\PasajeResource\RelationManagers;
use App\Models\Pasaje;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ImageUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;


class PasajeResource extends Resource
{
    protected static ?string $model = Pasaje::class;

    protected static ?string $navigationGroup = 'Administración Sitio Web';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(500)
                    ->rows(3),

                TextInput::make('precio')
                    ->label('Precio')
                    ->required()
                    ->numeric() 
                    ->step(0.01) 
                    ->placeholder('Ej: 0.50'),

                ColorPicker::make('color')
                    ->label('Color')
                    ->required(),
                FileUpload::make('imagen')
                    ->label('Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('img/paradas') // Carpeta donde se almacenarán las imágenes
                    ->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('titulo')->label('Título')->searchable(),
            Tables\Columns\TextColumn::make('precio')->label('Precio Unitario'),
            Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->formatStateUsing(fn($state) => "<span style='background-color: {$state}; border-radius: 50%; width: 20px; height: 20px; display: inline-block'></span>")
                    ->html(),
            Tables\Columns\ImageColumn::make('imagen')->label('Imagen'),
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
            'index' => Pages\ListPasajes::route('/'),
            'create' => Pages\CreatePasaje::route('/create'),
            'edit' => Pages\EditPasaje::route('/{record}/edit'),
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
