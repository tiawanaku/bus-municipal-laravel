<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RutaResource\Pages;
use App\Filament\Resources\RutaResource\RelationManagers;
use App\Models\Ruta;
use App\Models\Parada;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Components\HasMany;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class RutaResource extends Resource
{
    protected static ?string $model = Ruta::class;
    protected static ?string $navigationLabel = 'Rutas';


    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('nombre')
                ->label('Nombre Ruta')
                ->options([
                    'Ruta Norte' => 'Ruta Norte',
                    'Ruta Sur' => 'Ruta Sur',
                ])
                ->required(),

            // Lista de selección de paradas
            Forms\Components\CheckboxList::make('selected_paradas')
                ->options(Parada::pluck('nombre_parada', 'id_paradas'))
                ->label('Selecciona las paradas que pertenecen a esta ruta')
                ->default(static fn(?Ruta $record): array => $record ? $record->paradas->pluck('id_paradas')->toArray() : [])
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
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
            'index' => Pages\ListRutas::route('/'),
            'create' => Pages\CreateRuta::route('/create'),
            'edit' => Pages\EditRuta::route('/{record}/edit'),
        ];
    }

   
}
