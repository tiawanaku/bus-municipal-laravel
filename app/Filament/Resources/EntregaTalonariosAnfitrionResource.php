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
use IbrahimBougaoua\FilaProgress\Infolists\Components\ProgressBarEntry;
use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

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
            // 👥 Información de Personal
            Tables\Columns\TextColumn::make('personal_info')
                ->label('👥 Personal')
                ->html()
                ->getStateUsing(function ($record) {
                    $cajero = \App\Models\Cajero::find($record->entrega_talonario_id);
                    $anfitrion = \App\Models\Anfitrion::find($record->anfitrion_id);
                    
                    $cajeroNombre = $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                    $anfitrionNombre = $anfitrion ? $anfitrion->nombre . ' ' . $anfitrion->apellido_paterno . ' ' . $anfitrion->apellido_materno : 'No disponible';
                    
                    return "
                    <strong>Cajero:</strong> {$cajeroNombre}<br>
                    <strong>Anfitrión:</strong> {$anfitrionNombre}
                    ";
                })
                ->toggleable(isToggledHiddenByDefault: true),




            // 📋 Información General
            Tables\Columns\TextColumn::make('info_general')
                ->label('📋 Información General')
                ->html()
                ->getStateUsing(function ($record) {
                    return "
                    <strong>N° Autorización:</strong> {$record->numero_autorizacion}<br>
                    <strong>Fecha Entrega:</strong> " . date('d/m/Y', strtotime($record->fecha_entrega)) . "<br>
                    <strong>Tipo Talonarios:</strong> {$record->tipo_talonarios}
                    ";
                })
                ->toggleable(isToggledHiddenByDefault: true),

            // 🎫 Tickets Preferenciales
            Tables\Columns\TextColumn::make('preferenciales_info')
                ->label('🎫 Preferenciales')
                ->html()
                ->getStateUsing(function ($record) {
                    $colorTotal = '';
                    if ($record->total_boletos_preferenciales < 800) {
                        $colorTotal = '#dc2626'; // rojo
                    } elseif ($record->total_boletos_preferenciales >= 800 && $record->total_boletos_preferenciales <= 1500) {
                        $colorTotal = '#ea580c'; // naranja
                    } else {
                        $colorTotal = '#16a34a'; // verde
                    }

                    $colorRestante = $record->cantidad_restante_preferencial < 800 ? '#dc2626' : 
                                   ($record->cantidad_restante_preferencial < 2000 ? '#ea580c' : '#16a34a');
                    
                    return "
                    <div>
                        <strong>Cantidad:</strong> {$record->cantidad_preferenciales}<br>
                        <strong>Restante:</strong> <span style='color:{$colorRestante}; font-weight: bold;'>{$record->cantidad_restante_preferencial}</span><br>
                        <strong>Rango:</strong> {$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}<br>
                        <strong>Total Tickets:</strong> <span style='color:{$colorTotal}; font-weight: bold;'>{$record->total_boletos_preferenciales}</span><br>
                        <strong>Recaudo:</strong> <span style='color: #059669; font-weight: bold;'>Bs. " . number_format($record->total_aproximado_bolivianos_preferencial, 2, '.', ',') . "</span>
                    </div>
                    ";
                }),
// 🎟️ Barra de progreso: muestra boletos REGULARES restantes (disminuye al vender)
ProgressBar::make('regulares_progress')
    ->getStateUsing(function ($record) {
        $total = $record->cantidad_regulares ?: 1; // Evita división por cero
        $restantes = $record->cantidad_restante_regular ?? 0;

        $porcentajeRestante = round(($restantes / $total) * 100);
        $vendidos = $total - $restantes;

        return [
            'total' => 100,
            'progress' => $porcentajeRestante,
            'label' => "Quedan: {$restantes} de {$total} ({$porcentajeRestante}%)",
        ];
    })
    ->label('Boletos Regulares Restantes')
    ->extraAttributes([
        'class' => 'bg-orange-100',
        'style' => '--progress-value-display: block;'
    ]),


   
            // 🎟️ Tickets Regulares
            Tables\Columns\TextColumn::make('regulares_info')
                ->label('🎟️ Regulares')
                ->html()
                ->getStateUsing(function ($record) {
                    $colorTotal = '';
                    if ($record->total_boletos_regulares < 800) {
                        $colorTotal = '#dc2626'; // rojo
                    } elseif ($record->total_boletos_regulares >= 800 && $record->total_boletos_regulares <= 1500) {
                        $colorTotal = '#ea580c'; // naranja
                    } else {
                        $colorTotal = '#16a34a'; // verde
                    }

                    $colorRestante = $record->cantidad_restante_regular < 800 ? '#dc2626' : 
                                   ($record->cantidad_restante_regular < 2000 ? '#ea580c' : '#16a34a');
                    
                    return "
                    <div>
                        <strong>Cantidad:</strong> {$record->cantidad_regulares}<br>
                        <strong>Restante:</strong> <span style='color:{$colorRestante}; font-weight: bold;'>{$record->cantidad_restante_regular}</span><br>
                        <strong>Rango:</strong> {$record->rango_inicial_regular} - {$record->rango_final_regular}<br>
                        <strong>Total Tickets:</strong> <span style='color:{$colorTotal}; font-weight: bold;'>{$record->total_boletos_regulares}</span><br>
                        <strong>Recaudo:</strong> <span style='color: #059669; font-weight: bold;'>Bs. " . number_format($record->total_aproximado_bolivianos_regular, 2, '.', ',') . "</span>
                    </div>
                    ";
                }),

               // 🎫 Barra de progreso para boletos preferenciales (disminuye conforme se usan)
// 🎫 Barra de progreso que muestra lo que queda (disminuye al vender)
ProgressBar::make('preferenciales_progress')
    ->getStateUsing(function ($record) {
        $total = $record->cantidad_preferenciales ?: 1; // Para evitar división por cero
        $restantes = $record->cantidad_restante_preferencial ?? 0;

        $porcentajeRestante = round(($restantes / $total) * 100);
        $usados = $total - $restantes;

        return [
            'total' => 100,
            'progress' => $porcentajeRestante,
            'label' => "Quedan: $restantes / $total ({$porcentajeRestante}%)",
        ];
    })
    ->label('Boletos Preferenciales Restantes')
    ->extraAttributes([
        'class' => 'bg-blue-100',
        'style' => '--progress-value-display: block;'
    ]),



        Tables\Columns\TextColumn::make('total_recaudacion_bolivianos')
    ->label('💰 Total Recaudación')
    ->html()
    ->getStateUsing(function ($record) {
        return "
        <span style='font-size: 18px; color: #059669;'>
        Bs. " . number_format($record->total_recaudacion_bolivianos, 2, '.', ',') . "
        </span>
        ";
    }),

            // 📝 Observaciones y Fechas
            Tables\Columns\TextColumn::make('detalles_observaciones')
                ->label('📝 Observaciones')
                ->html()
                ->getStateUsing(function ($record) {
                    $observaciones = $record->observaciones ?: 'Sin observaciones';
                    return "
                    <div>
                        <strong>Observaciones:</strong><br>
                        <span style='font-size: 13px; line-height: 1.4;'>{$observaciones}</span><br><br>
                        <strong>Creado:</strong> " . date('d/m/Y H:i', strtotime($record->created_at)) . "<br>
                        <strong>Actualizado:</strong> " . date('d/m/Y H:i', strtotime($record->updated_at)) . "
                    </div>
                    ";
                })
                ->toggleable(isToggledHiddenByDefault: true),
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
