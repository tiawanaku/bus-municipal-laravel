<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;
use App\Filament\Resources\EntregaTalonariosAnfitrionResource\RelationManagers;
use App\Models\EntregaTalonariosAnfitrion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntregaTalonariosAnfitrionResource extends Resource
{
    protected static ?string $model = EntregaTalonariosAnfitrion::class;


    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $navigationLabel  = 'Inventario del Anfitrion';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Pregunta al usuario al principio del formulario
            Forms\Components\Select::make('tipo_talonario')
                ->label('¿Qué tipo de talonario desea registrar?')
                ->options([
                    'preferencial' => 'Preferenciales',
                    'regular' => 'Regulares',
                    'Preferenciales y Regulares' => 'Preferenciales y Regulares',
                ])
                ->reactive()
                ->required()
                ->columnSpanFull(),

            // Contenedor para paneles lado a lado
            Forms\Components\Grid::make(4)
                ->schema([
                    // Panel de Datos de Entrega (ocupa 2 columnas)
                    Forms\Components\Section::make('Datos de Entrega')
                        ->columnSpan(2)
                        ->columns(2)
                        ->schema([
                            Forms\Components\Select::make('entrega_talonario_id')  // Este campo luego se asigna a cajero_id
                                ->label('Cajero Patio de Ops.')
                                ->prefixIcon('heroicon-o-user')
                                ->options(function () {
                                    return \App\Models\Cajero::where('tipo_cajero', 'secundario')
                                        ->get()
                                        ->mapWithKeys(function ($cajero) {
                                            $fullName = $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                                            return [$cajero->id => $fullName];
                                        });
                                })
                                ->live()
                                ->required()
                                ->searchable()
                                ->columnSpanFull(),

                            Forms\Components\Select::make('anfitrion_id')
                                ->label('Anfitrión')
                                ->prefixIcon('heroicon-o-user')
                                ->relationship('anfitrion', 'nombre')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->nombre . ' ' . $record->apellido_paterno . ' ' . $record->apellido_materno)
                                ->required()
                                ->searchable()
                                ->columnSpanFull(), 
                        ]),

                    // Panel de Resumen Rápido (ocupa 2 columnas)
                    Forms\Components\Section::make('Resumen Rápido')
                        ->columnSpan(2)
                        ->icon('heroicon-o-chart-bar-square')
                        ->columns(3)
                        ->collapsible()
                        ->schema([

                               Forms\Components\DatePicker::make('fecha_entrega')
                                ->label('Fecha de Entrega')
                                ->prefixIcon('heroicon-o-calendar')
                                ->default(now())
                                ->disabled()  // Deshabilita el campo para que no se pueda cambiar
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\Placeholder::make('info_preferencial')
                                ->label('🔴 Preferencial')
                                ->content(function (Forms\Get $get) {
                                    try {
                                        // Buscar registros donde estado_preferencial sea igual a 1
                                        $entregaTalonarios = \App\Models\EntregaTalonario::where('estado_preferencial', 1)
                                            ->with('cajero')
                                            ->get();
                                        
                                        if ($entregaTalonarios->isEmpty()) {
                                            return '-- ⚫ Sin registros con estado 1';
                                        }
                                        
                                        $resultado = '';
                                        foreach ($entregaTalonarios as $entrega) {
                                            $cajeroNombre = $entrega->cajero ? 
                                                "{$entrega->cajero->nombre} {$entrega->cajero->apellido_paterno}" : 
                                                'Sin cajero';
                                            $cantidad = $entrega->cantidad_restante_preferencial ?? 0;
                                            $resultado .= "👤 {$cajeroNombre}: {$cantidad} 🟢\n";
                                        }
                                        
                                        return trim($resultado);
                                    } catch (\Exception $e) {
                                        return 'Error al cargar datos';
                                    }
                                }),

                            Forms\Components\Placeholder::make('info_regular')
                                ->label('🟠 Regular')
                                ->content(function (Forms\Get $get) {
                                    try {
                                        // Buscar registros donde estado_regular sea igual a 1
                                        $entregaTalonarios = \App\Models\EntregaTalonario::where('estado_regular', 1)
                                            ->with('cajero')
                                            ->get();
                                        
                                        if ($entregaTalonarios->isEmpty()) {
                                            return '-- ⚫ Sin registros con estado 1';
                                        }
                                        
                                        $resultado = '';
                                        foreach ($entregaTalonarios as $entrega) {
                                            $cajeroNombre = $entrega->cajero ? 
                                                "{$entrega->cajero->nombre} {$entrega->cajero->apellido_paterno}" : 
                                                'Sin cajero';
                                            $cantidad = $entrega->cantidad_restante_regular ?? 0;
                                            $resultado .= "👤 {$cajeroNombre}: {$cantidad} 🟢\n";
                                        }
                                        
                                        return trim($resultado);
                                    } catch (\Exception $e) {
                                        return 'Error al cargar datos';
                                    }
                                }),
                        ]),
                ]),

            Forms\Components\Section::make('Preferenciales')
                ->columns(4)
                ->schema([

                    // Campo 'Del'
                    Forms\Components\TextInput::make('preferencial_del')
                        ->label('Del')
                        ->prefixIcon('heroicon-o-arrow-down')
                        ->numeric()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $del = (int) $state;
                            $al = (int) $get('preferencial_al');

                            if ($del && $al && $al >= $del) {
                                $set('cantidad_talonarios_preferenciales', $al - $del + 1);
                            }
                        }),

                    // Campo 'Al'
                    Forms\Components\TextInput::make('preferencial_al')
                        ->label('Al')
                        ->prefixIcon('heroicon-o-arrow-up')
                        ->numeric()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $al = (int) $state;
                            $del = (int) $get('preferencial_del');

                            if ($del && $al && $al >= $del) {
                                $set('cantidad_talonarios_preferenciales', $al - $del + 1);
                            }
                        }),

                    // Campo 'Cantidad de Talonarios Preferenciales' (editable)
                    Forms\Components\TextInput::make('cantidad_talonarios_preferenciales')
                        ->label('Cantidad de Talonarios Preferenciales')
                        ->prefixIcon('heroicon-o-hashtag')
                        ->numeric()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $cantidad = (int) $state;
                            $del = (int) $get('preferencial_del');

                            if ($del && $cantidad > 0) {
                                $set('preferencial_al', $del + $cantidad - 1);
                            }
                        }),

                    Forms\Components\TextInput::make('rango_inicial_preferenciales')
                        ->label('Rango Inicial Preferenciales')
                        ->prefixIcon('heroicon-o-arrow-down')
                        ->numeric(),
                ])
                ->visible(fn(Forms\Get $get) => in_array($get('tipo_talonario'), ['preferencial', 'Preferenciales y Regulares'])),

            Forms\Components\Section::make('Regulares')
                ->columns(4)
                ->schema([

                    // Campo 'Del' (regulares)
                    Forms\Components\TextInput::make('regular_del')
                        ->label('Del')
                        ->prefixIcon('heroicon-o-arrow-down')
                        ->numeric()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $del = (int) $state;
                            $al = (int) $get('regular_al');

                            if ($del && $al && $al >= $del) {
                                $set('cantidad_talonarios_regulares', $al - $del + 1);
                            } else {
                                $set('cantidad_talonarios_regulares', null);
                            }
                        }),

                    // Campo 'Al' (regulares)
                    Forms\Components\TextInput::make('regular_al')
                        ->label('Al')
                        ->prefixIcon('heroicon-o-arrow-up')
                        ->numeric()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $al = (int) $state;
                            $del = (int) $get('regular_del');

                            if ($del && $al && $al >= $del) {
                                $set('cantidad_talonarios_regulares', $al - $del + 1);
                            } else {
                                $set('cantidad_talonarios_regulares', null);
                            }
                        }),

                    // Campo 'Cantidad de Talonarios Regulares' (editable y bidireccional)
                    Forms\Components\TextInput::make('cantidad_talonarios_regulares')
                        ->label('Cantidad de Talonarios Regulares')
                        ->prefixIcon('heroicon-o-hashtag')
                        ->numeric()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $cantidad = (int) $state;
                            $del      = (int) $get('regular_del');

                            if ($del && $cantidad > 0) {
                                $set('regular_al', $del + $cantidad - 1);
                            }
                        }),

                    Forms\Components\TextInput::make('rango_inicial_regulares')
                        ->label('Rango Inicial Regulares')
                        ->prefixIcon('heroicon-o-arrow-down')
                        ->numeric(),
                ])
                ->visible(fn(Forms\Get $get) => in_array($get('tipo_talonario'), ['regular', 'Preferenciales y Regulares'])),

            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3),
                ]),
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('entrega_talonario_id')
                    ->label('Cajer@s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        $cajero = \App\Models\Cajero::find($record->entrega_talonario_id);
                        return $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                    }),


                Tables\Columns\TextColumn::make('anfitrion_id')
                    ->label('Anfitrión')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        $anfitrion = \App\Models\Anfitrion::find($record->anfitrion_id);  // Cambia el modelo si se llama distinto
                        return $anfitrion ? $anfitrion->nombre . ' ' . $anfitrion->apellido_paterno . ' ' . $anfitrion->apellido_materno : 'No disponible';
                    }),

                Tables\Columns\TextColumn::make('numero_autorizacion')
                    ->label('N° Autorización')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('cantidad_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Cant. Preferencial'),

                Tables\Columns\TextColumn::make('rango_inicial_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Inicial Pref.'),

                Tables\Columns\TextColumn::make('rango_final_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Final Pref.'),

                Tables\Columns\TextColumn::make('total_boletos_preferenciales')
                    ->label('Total Tickets Pref.')
                    ->color(function ($state) {
                        if ($state < 800) {
                            return 'danger';    // rojo
                        } elseif ($state >= 800 && $state <= 1500) {
                            return 'warning';  // amarillo
                        } else {
                            return 'success';  // verde
                        }
                    }),


                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_preferencial')
                    ->label('Recaudo Preferecial Bs.')
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning'),

                Tables\Columns\TextColumn::make('cantidad_restante_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Cant. Restante Pref.'),

                Tables\Columns\TextColumn::make('cantidad_regulares')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Cant. Regulares'),

                Tables\Columns\TextColumn::make('rango_inicial_regular')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Inicial Reg.'),

                Tables\Columns\TextColumn::make('rango_final_regular')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Final Reg.'),

                Tables\Columns\TextColumn::make('total_boletos_regulares')
                    ->label('Total Tickets Reg.')
                    ->color(function ($state) {
                        if ($state < 800) {
                            return 'danger';    // rojo
                        } elseif ($state >= 800 && $state <= 1500) {
                            return 'warning';  // amarillo
                        } else {
                            return 'success';  // verde
                        }
                    }),


                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_regular')
                    ->label('Recaudo Regular Bs.')
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning'),

                Tables\Columns\TextColumn::make('cantidad_restante_regular')
                    ->label('Cant. Restante Reg.')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tipo_talonarios')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tipo Talonarios'),



                Tables\Columns\TextColumn::make('observaciones')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Observaciones')->limit(50),

                Tables\Columns\TextColumn::make('total_recaudacion_bolivianos')
                    ->label('Total Recaudación Bs.')
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning'),

                Tables\Columns\TextColumn::make('fecha_entrega')
                    ->label('Fecha de Entrega')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('created_at')->label('Creado')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                // Puedes agregar filtros aquí si deseas
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
            'index' => Pages\ListEntregaTalonariosAnfitrions::route('/'),
            'create' => Pages\CreateEntregaTalonariosAnfitrion::route('/create'),
            'edit' => Pages\EditEntregaTalonariosAnfitrion::route('/{record}/edit'),
        ];
    }
}
