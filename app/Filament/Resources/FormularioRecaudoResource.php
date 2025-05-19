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


class FormularioRecaudoResource extends Resource
{
   protected static ?string $model = FormularioRecaudo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Formulario Recaudo';
    protected static ?string $modelLabel = 'Formulario de Recaudo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

              Forms\Components\Select::make('anfitrion_id')
    ->label('Anfitrión')
    ->options(function () {
        return \App\Models\Anfitrion::all()->mapWithKeys(function ($item) {
            return [$item->id => $item->nombre . ' ' . $item->apellido_paterno . ' ' . $item->apellido_materno];
        })->toArray();
    })
    ->required(),

       


              Forms\Components\Select::make('bus_id')
    ->label('Bus')
    ->options(
        Bus::all()->pluck('numero_bus', 'id')->toArray()
    )
    ->required(),




              Forms\Components\Select::make('conductor_id')
    ->label('Conductor')
    ->options(
        Conductor::all()->mapWithKeys(function ($conductor) {
            return [
                $conductor->id => $conductor->nombre . ' ' . $conductor->apellido_paterno . ' ' . $conductor->apellido_materno,
            ];
        })->toArray()
    )
    ->required(),



Forms\Components\Select::make('ruta_id')
    ->label('Ruta')
    ->options([
        'norte' => 'Ruta Norte',
        'sur' => 'Ruta Sur',
    ])
    ->required(),


                Forms\Components\TextInput::make('cantidad_ventas_regulares')->numeric(),
                Forms\Components\TextInput::make('rango_inicial_regulares')->numeric(),
                Forms\Components\TextInput::make('rango_final_regulares')->numeric(),
                Forms\Components\TextInput::make('monto_recaudado_regular')->numeric()->prefix('Bs'),

                Forms\Components\TextInput::make('cantidad_ventas_preferenciales')->numeric(),
                Forms\Components\TextInput::make('rango_inicial_preferencial')->numeric(),
                Forms\Components\TextInput::make('rango_final_preferencial')->numeric(),
                Forms\Components\TextInput::make('monto_recaudado_preferencial')->numeric()->prefix('Bs'),

                Forms\Components\TextInput::make('total_recaudo_regular_preferencial')->numeric()->prefix('Bs'),
                
                Forms\Components\Select::make('horario')
                 ->label('Turno')
    ->options([
        'mañana' => 'Mañana',
        'tarde' => 'Tarde',
    ])
    ->required(),

                
                Forms\Components\Select::make('estado')
                    ->options([
                        0 => 'Vendido',
                        1 => 'En venta',
                        2 => 'Por vender',
                    ])
                    ->required()
                    ->label('Estado'),

                Forms\Components\Textarea::make('observaciones')->rows(3),
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
