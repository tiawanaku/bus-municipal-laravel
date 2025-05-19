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

use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\ImageColumn;

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

                Forms\Components\Select::make('id_ruta')
                    ->label('Ruta')
                    ->options(
                        \App\Models\Ruta::all()->pluck('nombre', 'id')
                    )
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
                    ->label('Ubicación (Lat/Lng)')
                    ->readonly(),



                // Card de paradas
                Card::make([

                    TextInput::make('descripcion')
                        ->label('Descripción')
                        ->required()
                        ->maxLength(255)
                        ->nullable(),
                    /* Para imagenes */
                    FileUpload::make('imagen')
                        ->label('Imagen')
                        ->image()
                        ->disk('public')
                        ->directory('img/paradas') // Carpeta donde se almacenarán las imágenes
                        ->nullable(),

                ]),





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

                TextColumn::make('ruta.nombre')->label('Ruta')->sortable()
                    ->searchable(),


                TextColumn::make('sentido')
                    ->label('Sentido')
                    ->sortable()
                    ->searchable(),


                TextInputColumn::make('orden')
                    ->label('Orden de parada')
                    ->placeholder('Ej: 1')
                    ->rules(['required', 'integer', 'min:1'])
                ,

                ImageColumn::make('imagen'),


            ])
            ->defaultSort('orden')

            ->filters([
                //
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
