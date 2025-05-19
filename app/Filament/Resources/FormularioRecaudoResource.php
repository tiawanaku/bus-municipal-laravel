<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormularioRecaudoResource\Pages;
use App\Filament\Resources\FormularioRecaudoResource\RelationManagers;
use App\Models\FormularioRecaudo;
use App\Models\Bus;
use App\Models\Conductor;
use App\Models\asignacionDeBus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Models\Ruta;
use Filament\Forms\Components\Section;

class FormularioRecaudoResource extends Resource
{
   protected static ?string $model = FormularioRecaudo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Formulario Recaudo';
     protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $modelLabel = 'Formulario de Recaudo';

 public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Sección Datos Generales
            Forms\Components\Section::make('Datos Generales')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('anfitrion_id')
                                ->label('Anfitrión')
                                ->options(function () {
                                    return \App\Models\Anfitrion::all()->mapWithKeys(function ($item) {
                                        return [$item->id => $item->nombre . ' ' . $item->apellido_paterno . ' ' . $item->apellido_materno];
                                    })->toArray();
                                })
                                ->required(),

                            Forms\Components\Select::make('conductor_id')
                                ->label('Conductor')
                                ->options(
                                    \App\Models\Conductor::all()->mapWithKeys(function ($conductor) {
                                        return [
                                            $conductor->id => $conductor->nombre . ' ' . $conductor->apellido_paterno . ' ' . $conductor->apellido_materno,
                                        ];
                                    })->toArray()
                                )
                                ->required(),

                            Forms\Components\Select::make('rutas')
                                ->label('Ruta')
                                ->options([
                                    'norte' => 'Ruta Norte',
                                    'sur' => 'Ruta Sur',
                                ])
                                ->required(),

                            Forms\Components\Select::make('horario')
                                ->label('Turno')
                                ->options([
                                    'mañana' => 'Mañana',
                                    'tarde' => 'Tarde',
                                ])
                                ->required(),

                           Forms\Components\Select::make('N_ficha')
                               ->label('Nº de Ficha')
                               ->options(array_combine(range(1, 15), range(1, 15)))
                               ->required(),

                               Forms\Components\Select::make('bus_id')
                                 ->label('Nº de Bus')
                                 ->options(function () {
                                    return \App\Models\Bus::all()->mapWithKeys(function ($item) {
                                     return [$item->id => $item->numero_bus];
                                    })->toArray();
                                 })
                                ->required(),
                                
                        ]),
                ]),

            // Sección Preferencial
            Forms\Components\Section::make('Preferencial')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('cantidad_ventas_preferenciales')
                                ->label('Tickets Vendidos Preferenciales')
                                ->numeric(),

                            Forms\Components\TextInput::make('rango_inicial_preferencial')
                                ->label('Rango Inicial')
                                ->numeric(),

                            Forms\Components\TextInput::make('monto_recaudado_preferencial')
                                ->label('Monto recaudado')
                                ->prefix('Bs')
                                ->numeric(),
                        ]),
                ]),

            // Sección Regular
            Forms\Components\Section::make('Regular')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('cantidad_ventas_regulares')
                                ->label('Tickets vendidos')
                                ->numeric(),

                            Forms\Components\TextInput::make('rango_inicial_regulares')
                                ->label('Rango Inicial')
                                ->numeric(),

                            Forms\Components\TextInput::make('monto_recaudado_regular')
                                ->label('Monto recaudado')
                                ->prefix('Bs')
                                ->numeric(),
                        ]),
                ]),

            // Sección Total
            Forms\Components\Section::make('Totales')
                ->schema([
                    Forms\Components\TextInput::make('total_recaudo_regular_preferencial')
                        ->label('Total Recaudo')
                        ->prefix('Bs')
                        ->numeric(),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('bus_id'),
                Tables\Columns\TextColumn::make('conductor_id'),
                Tables\Columns\TextColumn::make('ruta_id'),
                Tables\Columns\TextColumn::make('fecha_recaudo')->date(),

               BadgeColumn::make('estado_preferencial')
    ->label('Estado Preferencial')
    ->formatStateUsing(fn ($state) => match ($state) {
        0 => 'Vendido',
        1 => 'En venta',
        2 => 'Por vender',
        default => 'Desconocido'
    })
    ->colors([
        'danger' => 0,
        'primary' => 1,
        'success' => 2,
    ]),

            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListFormularioRecaudos::route('/'),
            'create' => Pages\CreateFormularioRecaudo::route('/create'),
            'edit' => Pages\EditFormularioRecaudo::route('/{record}/edit'),
        ];
    }
}
