<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioTalonariosResource\Pages;
use App\Filament\Resources\InventarioTalonariosResource\RelationManagers;
use App\Models\InventarioTalonarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Button;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use App\Models\Cajero;


class InventarioTalonariosResource extends Resource
{
    protected static ?string $model = InventarioTalonarios::class;

    protected static ?string $navigationLabel = 'Inventarios de Talonarios';
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Selección del tipo de talonario sin pregunta
            Forms\Components\Select::make('tipo_talonarios')
                ->label('Tipo de Talonario a Asignar')
                ->options([
                    'preferenciales' => 'Preferenciales',
                    'regulares'      => 'Regulares',
                    'ambos'          => 'Preferenciales y Regulares',
                ])
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    // Mostrar u ocultar secciones según la selección
                    if ($state === 'ambos') {
                        $set('show_preferenciales', true);
                        $set('show_regulares', true);
                    } elseif ($state === 'preferenciales') {
                        $set('show_preferenciales', true);
                        $set('show_regulares', false);
                    } else {
                        $set('show_preferenciales', false);
                        $set('show_regulares', true);
                    }
                }),

            Grid::make(2)->schema([
                Forms\Components\Select::make('cajero_id')
                    ->label('Cajero Principal')
                    ->prefixIcon('heroicon-o-user')
                    ->options(function () {
                        return \App\Models\Cajero::where('tipo_cajero', 'principal')
                            ->get()
                            ->mapWithKeys(function ($cajero) {
                                $fullName = "{$cajero->nombre} {$cajero->apellido_paterno} {$cajero->apellido_materno}";
                                return [$cajero->id => $fullName];
                            });
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\DatePicker::make('fecha_entrega')
                    ->label('Fecha de Entrega')
                    ->prefixIcon('heroicon-o-calendar')
                    ->default(now())
                    ->disabled()
                    ->dehydrated(true)
                    ->required(),
            ]),

            // Preferenciales
            Forms\Components\Section::make('Preferenciales')
                ->schema([
                    Grid::make(5)->schema([
                        Forms\Components\TextInput::make('cantidad_preferenciales')
                            ->label('Cantidad Preferenciales')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->numeric(),      

                        Forms\Components\TextInput::make('rango_inicial_preferencial')
                            ->label('Rango Inicial')
                            ->prefixIcon('heroicon-o-arrow-down')
                            ->numeric()
                            ->default(function () {
                                $ultimo = \App\Models\InventarioTalonarios::orderByDesc('rango_final_preferencial')->first();
                                return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                            }),

                        Forms\Components\TextInput::make('rango_final_preferencial')
                            ->label('Rango Final')
                            ->prefixIcon('heroicon-o-arrow-up')
                            ->disabled()
                            ->numeric(),

                        Forms\Components\TextInput::make('total_boletos_preferenciales')
                            ->label('Total Tickes')
                            ->prefixIcon('heroicon-o-ticket')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_aproximado_bolivianos')
                            ->label('Monto a Recaudar')
                            ->numeric(2)
                            ->disabled()
                            ->prefix('Bs.'),
                    ]),
                ])
                ->visible(fn($get) => $get('show_preferenciales')),

            // Regulares
            Forms\Components\Section::make('Regulares')
                ->schema([
                    Grid::make(5)->schema([
                        Forms\Components\TextInput::make('cantidad_regulares')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->numeric(),
                            

                        Forms\Components\TextInput::make('rango_inicial_regular')
                            ->label('Rango Inicial')
                            ->prefixIcon('heroicon-o-arrow-down')
                            ->numeric()
                            ->default(function () {
                                $ultimo = \App\Models\InventarioTalonarios::orderByDesc('rango_final_regular')->first();
                                return $ultimo ? $ultimo->rango_final_regular + 1 : 1;
                            }),

                        Forms\Components\TextInput::make('rango_final_regular')
                            ->label('Rango Final')
                            ->prefixIcon('heroicon-o-arrow-up')
                            ->disabled()
                            ->numeric()
                            ->rule(function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if ($value < $get('rango_inicial_regular')) {
                                        $fail('El rango final no puede ser menor que el rango inicial.');
                                    }
                                };
                            }),

                        Forms\Components\TextInput::make('total_boletos_regulares')
                            ->label('Total Tickes')
                            ->prefixIcon('heroicon-o-ticket')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_aproximado_bolivianos_regular')
                            ->numeric(2)
                            ->label('Monto a Recaudar') 
                            ->disabled()
                            ->prefix('Bs.'),
                    ]),
                ])
                ->visible(fn($get) => $get('show_regulares')),

            // Observaciones
            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3),
                ])
                ->columns(1),
        ]);
}




    public static function saving(InventarioTalonarios $record)
    {
        $inicioPref = request('rango_inicial_preferencial');
        $finPref = request('rango_final_preferencial');

        $inicioReg = request('rango_inicial_regular');
        $finReg = request('rango_final_regular');

        // Validar Preferenciales
        $existePref = \App\Models\InventarioTalonarios::where(function ($query) use ($inicioPref, $finPref) {
            $query
                ->whereBetween('rango_inicial_preferencial', [$inicioPref, $finPref])
                ->orWhereBetween('rango_final_preferencial', [$inicioPref, $finPref])
                ->orWhere(function ($q) use ($inicioPref, $finPref) {
                    $q->where('rango_inicial_preferencial', '<=', $inicioPref)
                        ->where('rango_final_preferencial', '>=', $finPref);
                });
        })->exists();

        if ($existePref) {
            throw \Filament\Notifications\Notification::make()
                ->title('Error en rango preferencial')
                ->body('El rango preferencial se solapa con uno existente.')
                ->danger()
                ->send();
        }

        // Validar Regulares
        $existeReg = \App\Models\InventarioTalonarios::where(function ($query) use ($inicioReg, $finReg) {
            $query
                ->whereBetween('rango_inicial_regular', [$inicioReg, $finReg])
                ->orWhereBetween('rango_final_regular', [$inicioReg, $finReg])
                ->orWhere(function ($q) use ($inicioReg, $finReg) {
                    $q->where('rango_inicial_regular', '<=', $inicioReg)
                        ->where('rango_final_regular', '>=', $finReg);
                });
        })->exists();

        if ($existeReg) {
            throw \Filament\Notifications\Notification::make()
                ->title('Error en rango regular')
                ->body('El rango regular se solapa con uno existente.')
                ->danger()
                ->send();
        }
    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cajero_id')
                    ->label('Cajero')
                    ->getStateUsing(function ($record) {
                        $cajero = \App\Models\Cajero::find($record->cajero_id);
                        return $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                    }),
                Tables\Columns\TextColumn::make('cantidad_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('rango_inicial_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_final_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_restante_preferencial')
                    ->label('Preferenciales Restantes')
                    ->badge() // opcional: muestra como etiqueta de color
                    ->color(function ($state) {
                        if ($state < 800) {
                            return 'danger'; // rojo
                        } elseif ($state < 1200) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_preferencial')
                    ->label('Total a Recaudar Pref. ')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_regulares')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_inicial_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_final_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_restante_regular')
                    ->label('Regulares Restantes')
                    ->badge()
                    ->color(function ($state) {
                        if ($state < 800) {
                            return 'danger'; // rojo
                        } elseif ($state < 1200) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_regulares')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_regular')
                    ->label('Total a Recaudar Regular')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('observaciones')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Agrega filtros aquí si es necesario
            ])
            ->searchable() // Esta línea habilita la búsqueda
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                //Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListInventarioTalonarios::route('/'),
            'create' => Pages\CreateInventarioTalonarios::route('/create'),
            'edit' => Pages\EditInventarioTalonarios::route('/{record}/edit'),
        ];
    }
}
