<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaTalonarioResource\Pages;
use App\Filament\Resources\EntregaTalonarioResource\RelationManagers;
use App\Models\EntregaTalonario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Section;

use Filament\Forms\Components\Placeholder;
use App\Models\InventarioTalonario;
use App\Models\InventarioTalonarios;

class EntregaTalonarioResource extends Resource
{
    protected static ?string $model = EntregaTalonario::class;

    protected static ?string $navigationGroup = 'Gestión de Talonarios';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('📦 Información de Lotes Activos')
                    ->schema([
                        // Estado Regular
                        Forms\Components\Placeholder::make('estado_lote_regular')
                            ->label('Estado del Lote Regular')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_regular', 1)->first();
                                return $lote ? '🟢 Asignable' : '❌ No Asignable';
                            }),

                        // Rango Regular
                        Forms\Components\Placeholder::make('rango_regular_activo')
                            ->label('Rango del Lote Regular')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_regular', 1)->first();
                                return $lote
                                    ? 'Desde ' . $lote->rango_inicial_regular . ' hasta ' . $lote->rango_final_regular
                                    : 'No hay lote activo';
                            }),

                        // Estado Preferencial
                        Forms\Components\Placeholder::make('estado_lote_preferencial')
                            ->label('Estado del Lote Preferencial')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_preferencial', 1)->first();
                                return $lote ? '🟢 Asignable' : '❌ No Asignable';
                            }),

                        // Rango Preferencial
                        Forms\Components\Placeholder::make('rango_preferencial_activo')
                            ->label('Rango del Lote Preferencial')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_preferencial', 1)->first();
                                return $lote
                                    ? 'Desde ' . $lote->rango_inicial_preferencial . ' hasta ' . $lote->rango_final_preferencial
                                    : 'No hay lote activo';
                            }),
                    ])
                    ->columns(4)
                    ->extraAttributes(['class' => 'bg-white rounded-xl p-4 shadow-sm']),



                Section::make('Datos de Entrega')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([

                                Forms\Components\Select::make('users_id')
                                    ->label('Responsable de Entrega')
                                    ->prefixIcon('heroicon-o-user-circle')
                                    ->options(function () {
                                        return \App\Models\User::all()->pluck('name', 'id')->mapWithKeys(function ($item, $key) {
                                            $user = \App\Models\User::find($key);
                                            $fullName = $user->name . ' ' . $user->apellido_paterno . ' ' . $user->apellido_materno;
                                            return [$key => $fullName];
                                        });
                                    })
                                    ->nullable(),

                                Forms\Components\Select::make('cajero_id')
                                    ->label('Cajero')
                                    ->prefixIcon('heroicon-o-user')
                                    ->options(function () {
                                        return \App\Models\Cajero::all()->pluck('nombre', 'id')->mapWithKeys(function ($item, $key) {
                                            $cajero = \App\Models\Cajero::find($key);
                                            $fullName = $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                                            return [$key => $fullName];
                                        });
                                    })
                                    ->required(),

                                Forms\Components\DatePicker::make('fecha_entrega')
                                    ->label('Fecha de Entrega')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->required(),
                            ])
                    ]),

                Section::make('Talonarios Preferenciales')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('cantidad_preferenciales')
                                    ->label('Talonarios Preferenciales')
                                    ->prefixIcon('heroicon-o-calculator')
                                    ->nullable(),


                                // Forms\Components\TextInput::make('cantidad_preferenciales')
                                //  ->label('Talonarios Preferenciales')
                                //  ->prefixIcon('heroicon-o-calculator')
                                //  ->required()
                                //  ->reactive()
                                // ->afterStateUpdated(function (callable $set, $state) {
                                //    $total_boletos = (int) $state * 50;
                                //     $set('total_boletos_preferenciales', $total_boletos);
                                //     $set('total_final_preferenciales', $total_boletos * 1);
                                // }),



                                Forms\Components\TextInput::make('rango_inicial_preferencial')
                                    ->label('Rango Inicial')
                                    ->prefixIcon('heroicon-o-arrow-down')
                                    ->nullable(),

                                Forms\Components\TextInput::make('rango_final_preferencial')
                                    ->label('Rango Final')
                                    ->prefixIcon('heroicon-o-arrow-up')
                                    ->nullable(),

                                Forms\Components\TextInput::make('cantidad_restante_preferencial')
                                    ->label('Cantidad Restante')
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->nullable(),

                                //Forms\Components\TextInput::make('total_boletos_preferenciales')
                                //->label('Total de Boletos Preferenciales')
                                // ->disabled()
                                // ->dehydrated(false), // para que no se guarde en la base de datos

                                //Forms\Components\TextInput::make('total_final_preferenciales')
                                //  ->label('Total Talonarios en BS')
                                //->disabled()
                                //->dehydrated(false),

                            ])
                    ]),





                Section::make('Talonarios Regulares')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([


                                Forms\Components\TextInput::make('cantidad_regulares')
                                    ->label('Talonarios Preferenciales')
                                    ->prefixIcon('heroicon-o-calculator')
                                    ->nullable(),

                                // Forms\Components\TextInput::make('cantidad_regulares')
                                // ->label('Cantidad Talonarios Regulares')
                                // ->prefixIcon('heroicon-o-calculator')
                                // ->nullable()
                                // ->reactive()
                                //->afterStateUpdated(function (callable $set, $state) {
                                //   $total_boletos = (int) $state * 50;
                                //  $set('total_boletos_regulares', $total_boletos);
                                // $set('total_final_regulares', $total_boletos * 1.5);
                                // }),


                                Forms\Components\TextInput::make('rango_inicial_regular')
                                    ->label('Rango Inicial')
                                    ->prefixIcon('heroicon-o-arrow-down')
                                    ->nullable(),

                                Forms\Components\TextInput::make('rango_final_regular')
                                    ->label('Rango Final')
                                    ->prefixIcon('heroicon-o-arrow-up')
                                    ->nullable(),

                                Forms\Components\TextInput::make('cantidad_restante_regular')
                                    ->label('Cantidad Restante')
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->nullable(),

                                // Forms\Components\TextInput::make('total_boletos_regulares')
                                // ->label('Total de Boletos Regulares')
                                // ->disabled()
                                //->dehydrated(false),

                                // Forms\Components\TextInput::make('total_final_regulares')
                                //->label('Total Final Regulares (Bs.)')
                                // ->disabled()
                                // ->dehydrated(false),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('users_id')
                    ->label('Responsable de Entrega')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        $user = \App\Models\User::find($record->users_id);
                        return $user ? $user->name . ' ' . $user->apellido_paterno . ' ' . $user->apellido_materno : 'No disponible';
                    }),

                Tables\Columns\TextColumn::make('cajero_id')
                    ->label('Cajero')
                    ->searchable()
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
                    ->badge()
                    ->color(function ($state) {
                        if ($state < 400) {
                            return 'danger'; // rojo
                        } elseif ($state < 800) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),



                Tables\Columns\TextColumn::make('cantidad_regulares')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('rango_inicial_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_final_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_restante_regular')
                    ->label('Regulares Restantes')
                    ->badge()
                    ->color(function ($state) {
                        if ($state < 400) {
                            return 'danger'; // rojo
                        } elseif ($state < 800) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_regulares')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_regular')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),



                Tables\Columns\TextColumn::make('fecha_entrega')
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
            'index' => Pages\ListEntregaTalonarios::route('/'),
            'create' => Pages\CreateEntregaTalonario::route('/create'),
            'edit' => Pages\EditEntregaTalonario::route('/{record}/edit'),
        ];
    }
}