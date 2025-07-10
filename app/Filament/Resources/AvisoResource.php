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
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\RichEditor;
use ArberMustafa\FilamentLocationPickrField\Forms\Components\LocationPickr;

class AvisoResource extends Resource
{
    protected static ?string $model = Aviso::class;
    protected static ?string $navigationGroup = 'Administración Sitio Web';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('noticia')
                    ->label('Tipo de Noticia')
                    ->options([
                        'Cambio de Ruta' => 'Cambio de Ruta',
                        'Bloqueo de Vías' => 'Bloqueo de Vías',
                        
                        'Suspención del servicio' => 'Suspención del servicio',
                        'Otro' => 'Otro',
                    ])
                    ->required()
                    ->reactive(),

                Textarea::make('razon')
                    ->label('Razón del aviso')
                    ->placeholder('Ej. Trabajo de infraestructura programada')
                    ->rows(2)
                    ->required(),

                Forms\Components\DateTimePicker::make('inicio_periodo')
                    ->label('Inicio de Periodo')
                    ->default(now())
                    ->required(),

                Forms\Components\DateTimePicker::make('fin_periodo')
                    ->label('Fin de Periodo')
                    ->default(now())
                    ->nullable(),



                Forms\Components\Textarea::make('paradas_afectadas')
                    ->label('Paradas Afectadas')
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

                            $set('ubicacion', json_encode(['lat' => $lat, 'lng' => $lng]));
                        }
                    }),

                // Campo de texto para mostrar las coordenadas
                Forms\Components\TextInput::make('ubicacion')
                    ->label('Ubicación (Lat/Lng)')
                    ->readonly(),

                 RichEditor::make('detalle')
                    ->label('Detalles')
                    ->disableToolbarButtons(['attachFiles'])
                    ->default('<p>Estimados usuarios debido a:</p>') 
                    ->required(),

                Forms\Components\Toggle::make('status')
                    ->label('¿Publicado?')
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('noticia'),
                Tables\Columns\TextColumn::make('inicio_periodo'),
                Tables\Columns\TextColumn::make('fin_periodo'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Publicado'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre del Usuario'),

            ])
            ->filters([
                //
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Publicado')
                    ->placeholder('Todos los Avisos')
                    ->trueLabel('Solo publicados')
                    ->falseLabel('Solo no publicados'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewAction::make()
                    ->form([
                        TextInput::make('razon')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('detalle')
                        ,
                        ToggleButtons::make('status')
                            ->label('Publicar?')
                            ->boolean()
                            ->grouped()
                        // ...
                    ]),
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
