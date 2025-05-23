<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RutaResource\Pages;
use App\Filament\Resources\RutaResource\RelationManagers;
use App\Models\Ruta;
use App\Models\Parada;
use Illuminate\Database\Eloquent\Model;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\RutaResource\RelationManagers\ParadasRelationManager;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\ColorPicker;
use Filament\Infolists\Components\ColorEntry;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;




class RutaResource extends Resource
{
    protected static ?string $model = Ruta::class;
    protected static ?string $navigationLabel = 'Rutas';


    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('nombre')
                ->label('Nombre Ruta')
                ->options([
                    'Ruta Norte' => 'Ruta Norte',
                    'Ruta Sur' => 'Ruta Sur',
                ])
                ->required(),
            /* MAp Picker */
            Map::make('recorrido')
                ->label('Recorrido del bus')
                ->columnSpanFull()
                ->default(fn(?Model $record) => $record?->recorrido)
                ->dehydrated(true)
                ->reactive()
                ->defaultLocation(latitude: -16.5198, longitude: -68.20793)
                ->zoom(15)
                ->drawPolyline(true)

                ->dragMode(true)

                ->geoMan(true)
                ->geoManEditable(true)
                ->showMarker(false)
                ->drawMarker(false)
                ->drawPolygon(false)
                ->cutPolygon(false)
                ->editPolygon(false)
                ->drawRectangle(false)
                ->drawCircle(false)
                ->drawCircleMarker(false)
                ->drawText(false)
                ->deleteLayer(false)
                ->rotateMode(false),

            /* Imagen de fondo en la página Rutas y colores para los popup de paradas y el recorrido */
            FileUpload::make('imagen')
                ->label('Imagen')
                ->image()
                ->disk('public')
                ->directory('img/rutas')
                ->nullable()
                ->maxSize(1024),

            TextArea::make('descripcion')
                ->label('Descripción')
                ->nullable()
                ->maxLength(500),

            ColorPicker::make('color')
                ->label('Color')
                ->required(),
            /* Link para el video iframe */
            TextInput::make('video_link')
                ->label('Link del video')
                ->url()
                ->placeholder('Ejemplo: https://www.facebook.com/ElAltoAlcaldia/videos/...')
                ->columnSpanFull()
                ->nullable(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                ImageColumn::make('imagen')
                    ->square(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->formatStateUsing(fn($state) => "<span style='background-color: {$state}; border-radius: 50%; width: 20px; height: 20px; display: inline-block'></span>")
                    ->html(),
                Tables\Columns\TextColumn::make('paradas.nombre_parada')
                    ->label('Paradas Asociadas')
                    ->getStateUsing(fn(Ruta $record) => $record->paradas->pluck('nombre_parada')->join(', ')),

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
            ParadasRelationManager::class,
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
