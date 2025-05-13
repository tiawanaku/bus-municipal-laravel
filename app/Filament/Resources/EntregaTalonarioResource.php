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


use Filament\Notifications\Notification;

use Filament\Forms\Components\Section;

use Filament\Forms\Components\Placeholder;
use App\Models\InventarioTalonario;
use Illuminate\Validation\ValidationException;

class EntregaTalonarioResource extends Resource
{
    protected static ?string $model = EntregaTalonario::class;

    protected static ?string $navigationGroup = 'Gestión de Talonarios';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pregunta para seleccionar el tipo de talonario a asignar
                Forms\Components\Select::make('tipo_talonarios') // No 'tipo_asignacion'
                    ->label('¿Qué tipo de talonario va a asignar?')
                    ->options([
                        'preferenciales' => 'Preferenciales',
                        'regulares'      => 'Regulares',
                        'ambos'          => 'Preferenciales y Regulares',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
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


                // Sección con información de los lotes activos
                Section::make('📦 Información de Lotes Activos')
                    ->schema([

                        // Información del Lote Regular
                        Placeholder::make('estado_lote_regular')
                            ->label('Estado del Lote Regular')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_regular', 1)->first();
                                return $lote ? '🟢 Asignable' : '❌ No Asignable';
                            })
                            ->visible(fn($get) => $get('show_regulares') === true),

                        Placeholder::make('rango_regular_activo')
                            ->label('Rango del Lote Regular')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_regular', 1)->first();
                                return $lote
                                    ? 'Desde ' . $lote->rango_inicial_regular . ' hasta ' . $lote->rango_final_regular
                                    : 'No hay lote activo';
                            })
                            ->visible(fn($get) => $get('show_regulares') === true),

                        Placeholder::make('cantidad_restante_regular')
                            ->label('Cantidad Restante Regular')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_regular', 1)->first();
                                return $lote
                                    ? $lote->cantidad_restante_regular
                                    : 'No disponible';
                            })
                            ->visible(fn($get) => $get('show_regulares') === true),

                        // Información del Lote Preferencial
                        Placeholder::make('estado_lote_preferencial')
                            ->label('Estado del Lote Preferencial')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_preferencial', 1)->first();
                                return $lote ? '🟢 Asignable' : '❌ No Asignable';
                            })
                            ->visible(fn($get) => $get('show_preferenciales') === true),

                        Placeholder::make('rango_preferencial_activo')
                            ->label('Rango del Lote Preferencial')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_preferencial', 1)->first();
                                return $lote
                                    ? 'Desde ' . $lote->rango_inicial_preferencial . ' hasta ' . $lote->rango_final_preferencial
                                    : 'No hay lote activo';
                            })
                            ->visible(fn($get) => $get('show_preferenciales') === true),

                        Placeholder::make('cantidad_restante_preferencial')
                            ->label('Cantidad Restante Preferencial')
                            ->content(function () {
                                $lote = \App\Models\InventarioTalonarios::where('estado_preferencial', 1)->first();
                                return $lote
                                    ? $lote->cantidad_restante_preferencial
                                    : 'No disponible';
                            })
                            ->visible(fn($get) => $get('show_preferenciales') === true),

                    ])
                    ->columns(6)
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
                                    ->required()
                                    ->default(today()), // Esto establece la fecha actual como valor por defecto
                            ])
                    ]),

                // Sección solo para Preferenciales
                Section::make('Talonarios Preferenciales')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([


                                Forms\Components\TextInput::make('cantidad_preferenciales')
                                    ->label('Talonarios Preferenciales')
                                    ->prefixIcon('heroicon-o-calculator')
                                    ->numeric()
                                    ->nullable()
                                    ->rule(function () {
                                        return function (string $attribute, $value, $fail) {
                                            $lote = \App\Models\InventarioTalonarios::where('estado_preferencial', 1)->first();

                                            if (!$lote) {
                                                $fail('❌ No hay lote preferencial activo para asignar.');
                                                return;
                                            }

                                            if ($value > $lote->cantidad_restante_preferencial) {
                                                $fail('⚠️ No puedes asignar más talonarios de los disponibles (' . $lote->cantidad_restante_preferencial . ').');
                                            }
                                        };
                                    }),

                                Forms\Components\TextInput::make('rango_inicial_preferencial')
                                    ->label('Rango Inicial')
                                    ->prefixIcon('heroicon-o-arrow-down')
                                    ->numeric()
                                    ->default(function () {
                                        $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_preferencial')->first();
                                        return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                                    }),

                                Forms\Components\TextInput::make('rango_final_preferencial')
                                    ->label('Rango Final')
                                    ->prefixIcon('heroicon-o-arrow-up')
                                    ->numeric()
                                    ->rule(function ($get) {
                                        return function ($attribute, $value, $fail) use ($get) {
                                            if ($value < $get('rango_inicial_preferencial')) {
                                                $fail('El rango final preferencial no puede ser menor que el rango inicial.');
                                            }
                                        };
                                    }),

                                Forms\Components\TextInput::make('cantidad_restante_preferencial')
                                    ->label('Cantidad Restante')
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->nullable(),
                            ]),
                    ])
                    ->hidden(fn($get) => ! $get('show_preferenciales')),

                // Sección solo para Regulares
                Section::make('Talonarios Regulares')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('cantidad_regulares')
                                    ->label('Talonarios Regulares')
                                    ->prefixIcon('heroicon-o-calculator')
                                    ->numeric()
                                    ->nullable()
                                    ->rule(function () {
                                        return function (string $attribute, $value, $fail) {
                                            $lote = \App\Models\InventarioTalonarios::where('estado_regular', 1)->first();

                                            if (!$lote) {
                                                $fail('❌ No hay lote regular activo para asignar.');
                                                return;
                                            }

                                            if ($value > $lote->cantidad_restante_regular) {
                                                $fail('⚠️ No puedes asignar más talonarios de los disponibles (' . $lote->cantidad_restante_regular . ').');
                                            }
                                        };
                                    }),

                                Forms\Components\TextInput::make('rango_inicial_regular')
                                    ->label('Rango Inicial')
                                    ->prefixIcon('heroicon-o-arrow-down')
                                    ->numeric()
                                    ->default(function () {
                                        $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_regular')->first();
                                        return $ultimo ? $ultimo->rango_final_regular + 1 : 1;
                                    }),

                                Forms\Components\TextInput::make('rango_final_regular')
                                    ->label('Rango Final')
                                    ->prefixIcon('heroicon-o-arrow-up')
                                    ->numeric()
                                    ->rule(function ($get) {
                                        return function ($attribute, $value, $fail) use ($get) {
                                            if ($value < $get('rango_inicial_regular')) {
                                                $fail('El rango final no puede ser menor que el rango inicial.');
                                            }
                                        };
                                    }),

                                Forms\Components\TextInput::make('cantidad_restante_regular')
                                    ->label('Cantidad Restante')
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->nullable(),
                            ]),
                    ])
                    ->hidden(fn($get) => ! $get('show_regulares')),
            ]);
    }

    public static function saving(EntregaTalonario $record)
    {
        $inicioPref = request('rango_inicial_preferencial');
        $finPref = request('rango_final_preferencial');

        $inicioReg = request('rango_inicial_regular');
        $finReg = request('rango_final_regular');

        // Validar Preferenciales
        $existePref = \App\Models\EntregaTalonario::where(function ($query) use ($inicioPref, $finPref) {
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

            throw new \Exception('Rango preferencial inválido.');
        }

        // Validar Regulares
        $existeReg = \App\Models\EntregaTalonario::where(function ($query) use ($inicioReg, $finReg) {
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

            throw new \Exception('Rango regular inválido.');
        }
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

                Tables\Columns\TextColumn::make('estado_preferencial')
                    ->label('Estado Preferencial')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            0 => 'asignado',
                            1 => 'asignable',
                            2 => 'no asignable',
                            default => 'desconocido',
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            0 => 'danger',       // asignado → verde
                            1 => 'success',       // asignable → azul
                            2 => 'primary',         // no asignable → rojo
                            default => 'gray',    // desconocido
                        };
                    }),



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

                Tables\Columns\TextColumn::make('estado_regular')
                    ->label('Estado Preferencial')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            0 => 'asignado',
                            1 => 'asignable',
                            2 => 'no asignable',
                            default => 'desconocido',
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            0 => 'danger',       // asignado → verde
                            1 => 'success',       // asignable → azul
                            2 => 'primary',        // no asignable → rojo
                            default => 'gray',    // desconocido
                        };
                    }),


                Tables\Columns\TextColumn::make('fecha_entrega')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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