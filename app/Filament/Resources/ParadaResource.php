<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParadaResource\Pages;
use App\Filament\Resources\ParadaResource\RelationManagers;
use App\Models\Parada;
use Filament\Forms;
use Filament\Forms\Form;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use App\Forms\Components\LeafletMap;
use Filament\Forms\Components\TextInput;


use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\DB;
use ArberMustafa\FilamentLocationPickrField\Forms\Components\LocationPickr;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Set;

class ParadaResource extends Resource
{
    protected static ?string $model = Parada::class;

    protected static ?string $navigationGroup = 'Administrador del Sistema';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([


                Forms\Components\TextInput::make('nombre_parada')
                    ->label('Nombre(s)')
                    ->required()
                    ->maxLength(255),

                    Forms\Components\Select::make('sentido')
                    ->label('Sentido')
                    ->options([
                        'Ida' => 'Sentido Ida',
                        'Vuelta' => 'Sentido Vuelta',
                    ])
                    ->required(),



                // Componente LOcation Picker
                LocationPickr::make('lat_long_v1')
                    ->label('Seleccionar ubicación')

                    ->mapControls([
                        'mapTypeControl' => true,
                        'scaleControl' => true,
                        'streetViewControl' => true,
                        'rotateControl' => true,
                        'fullscreenControl' => true,
                        'zoomControl' => false,
                    ])
                    ->defaultZoom(15)
                    ->draggable()
                    ->clickable()
                    ->height('40vh')
                    // Ubicación por defecto 
                    ->defaultLocation(function ($record) {
                        if ($record && $record->lat_long) {
                            $location = json_decode($record->lat_long, true);
                            return [$location['lat'], $location['lng']];
                        }
                        return [-16.52546755669295, -68.1826633779974];
                    })
                    ->myLocationButtonLabel('My location')

                    //Obtener los datos del marker y convertirlo en Json
                    ->afterStateUpdated(function ($state, callable $set) {

                        if ($state && is_array($state)) {
                            $lat = $state['lat'];
                            $lng = $state['lng'];

                            $set('lat_long', json_encode(['lat' => $lat, 'lng' => $lng]));
                        }
                    }),

                // Campo de texto para mostrar las coordenadas
                Forms\Components\TextInput::make('lat_long')
                    ->label('Ubicación (Lat/Lng)'),


            ]);


    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_parada')
                    ->label('Nombre de la Parada')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('ruta.nombre')->label('Ruta')->sortable(),


                TextColumn::make('sentido')
                    ->label('Sentido')
                    ->sortable()
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
            'index' => Pages\ListParadas::route('/'),
            'create' => Pages\CreateParada::route('/create'),
            'edit' => Pages\EditParada::route('/{record}/edit'),
        ];
    }

}
