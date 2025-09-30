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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use App\Models\InventarioTalonarios;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;

use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ActionGroup;
use App\Models\Cajero;

use Illuminate\Support\HtmlString;


class EntregaTalonarioResource extends Resource
{
    protected static ?string $model = EntregaTalonario::class;
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Inventario del Cajero';

   public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            // PASO 1: SELECCIÓN DE INVENTARIO Y CAJERO CON LÓGICA FIFO
            Section::make('Paso 1: Selección de Inventario y Cajero')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('inventario_id')
                            ->label('Seleccione el Inventario')
                            ->prefixIcon('heroicon-o-archive-box')
                            ->options(function () {
                                // Aplicar lógica FIFO: ordenar por fecha de creación más antigua primero
                                // Excluir registros con estado "agotado"
                                return \App\Models\InventarioTalonarios::where('estado', 'disponible')
                                    ->where('estado', '!=', 'agotado') // Excluir los agotados
                                    ->orderBy('created_at', 'asc') // FIFO: primero los más antiguos
                                    ->get()
                                    ->mapWithKeys(function ($inventario) {
                                        $cajeroPrincipal = \App\Models\Cajero::find($inventario->cajero_id);
                                        $nombreCajero = $cajeroPrincipal ? 
                                            "{$cajeroPrincipal->nombre} {$cajeroPrincipal->apellido_paterno}" : 
                                            'Sin asignar';
                                        
                                        return [$inventario->id => "{$nombreCajero}"];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $inventario = \App\Models\InventarioTalonarios::find($state);
                                    if ($inventario) {
                                        // Resetear campos dependientes
                                        $set('tipo_talonario', null);
                                        $set('preferencial_del', null);
                                        $set('preferencial_al', null);
                                        $set('regular_del', null);
                                        $set('regular_al', null);
                                    }
                                }
                            }),

                        Select::make('cajero_id')
                            ->label('Seleccione el Cajero de Patio')
                            ->prefixIcon('heroicon-o-user')
                            ->options(function () {
                                return \App\Models\Cajero::where('tipo_cajero', 'secundario')
                                    ->get()
                                    ->mapWithKeys(function ($cajero) {
                                        $fullName = "{$cajero->nombre} {$cajero->apellido_paterno} {$cajero->apellido_materno}";
                                        return [$cajero->id => $fullName];
                                    });
                            })
                            ->searchable()
                            ->required(),
                    ]),
                    
                    // Información del inventario seleccionado - REORGANIZADO
                    self::getInventarioInfoPlaceholder(),
                ])
                ->columns(1)
                ->collapsible(),

            // PASO 2: TIPO DE TALONARIO Y FECHA - SE MUESTRA AUTOMÁTICAMENTE AL SELECCIONAR INVENTARIO
            Section::make('Paso 2: Tipo de Talonario y Fecha')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('tipo_talonario')
                            ->label('¿Qué tipo de talonario entregará?')
                            ->options(function (callable $get) {
                                $inventarioId = $get('inventario_id');
                                $options = [];
                                
                                if ($inventarioId) {
                                    $inventario = \App\Models\InventarioTalonarios::find($inventarioId);
                                    if ($inventario) {
                                        // Solo mostrar opciones basadas en el stock disponible
                                        if ($inventario->cantidad_restante_preferencial > 0) {
                                            $options['preferencial'] = 'Preferencial';
                                        }
                                        if ($inventario->cantidad_restante_regular > 0) {
                                            $options['regular'] = 'Regular';
                                        }
                                        if ($inventario->cantidad_restante_preferencial > 0 && $inventario->cantidad_restante_regular > 0) {
                                            $options['Preferenciales y Regulares'] = 'Preferenciales y Regulares';
                                        }
                                    }
                                }
                                
                                return $options;
                            })
                            ->reactive()
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->placeholder('Seleccione el tipo')
                            ->disabled(fn($get) => !$get('inventario_id'))
                            ->helperText(function ($get) {
                                $inventarioId = $get('inventario_id');
                                if (!$inventarioId) {
                                    return 'Primero complete el Paso 1';
                                }
                                
                                $inventario = \App\Models\InventarioTalonarios::find($inventarioId);
                                if (!$inventario) {
                                    return 'Inventario no encontrado';
                                }
                                
                                return 'Seleccione el tipo de talonario a entregar';
                            }),

                        Forms\Components\DatePicker::make('fecha_entrega')
                            ->label('Fecha de Entrega')
                            ->default(\Carbon\Carbon::now()->toDateString())
                            ->disabled()
                            ->required()
                            ->prefixIcon('heroicon-o-calendar')
                            ->helperText('Fecha automática del sistema'),
                    ]),
                ])
                ->columns(1)
                ->visible(fn($get) => !empty($get('inventario_id')))
                ->collapsible(),

           // PASO 3: DETALLES DE PREFERENCIALES
Forms\Components\Section::make('Paso 3: Detalles de Talonarios Preferenciales')
    ->visible(fn($get) => in_array($get('tipo_talonario'), ['preferencial', 'Preferenciales y Regulares']) && $get('inventario_id'))
    ->schema([
        Forms\Components\Grid::make(3)->schema([

TextInput::make('preferencial_del')
    ->label('Del')
    ->prefixIcon('heroicon-o-arrow-down')
    ->numeric()
    ->required()
    ->minValue(1)
    ->reactive()
    ->default(function () {
        $ultimo = \App\Models\EntregaTalonario::orderByDesc('preferencial_al')->first();
        return $ultimo ? $ultimo->preferencial_al + 1 : 1;
    }),

            TextInput::make('preferencial_al')
                    ->label('Al')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->gt('preferencial_del') // ← Validación simple
                    ->helperText('Debe ser mayor que "Del"'),
                    
            Forms\Components\TextInput::make('rango_inicial_preferencial')
                ->label('Rango Inicial Facturas')
                ->prefixIcon('heroicon-o-document')
                ->numeric()
                ->required()
                ->default(function () {
                    $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_preferencial')->first();
                    return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                })
                ->reactive() // ← AGREGAR ESTO
                ->helperText('Auto-generado'),
        ]),
        
        // Resumen de preferenciales
        Placeholder::make('resumen_pref')
            ->label('📋 Resumen de Preferenciales')
            ->content(function ($get) {
                $del = (int) $get('preferencial_del');
                $al = (int) $get('preferencial_al');
                $inventarioId = $get('inventario_id');
                $stockDisponible = 0;
                
                if ($inventarioId) {
                    $inventario = \App\Models\InventarioTalonarios::find($inventarioId);
                    $stockDisponible = $inventario?->cantidad_restante_preferencial ?? 0;
                }
                
                if ($del && $al && $al >= $del) {
                    $cantidad = $al - $del + 1;
                    $mensaje = "**{$cantidad}** talonario(s) preferencial(es)";
                    $mensaje .= " (Del {$del} al {$al})";
                    
                    if ($stockDisponible > 0) {
                        if ($cantidad > $stockDisponible) {
                            $mensaje .= " ❌ **Stock insuficiente** (Disponible: {$stockDisponible})";
                        } else {
                            $mensaje .= " ✅ **Stock disponible** (Restante: " . ($stockDisponible - $cantidad) . ")";
                        }
                    }
                    
                    return $mensaje;
                }
                return 'Complete los campos "Del" y "Al" para ver el resumen';
            })
            ->extraAttributes(['class' => 'text-sm bg-green-50 p-2 rounded']),
    ])
    ->columns(1)
    ->collapsible(),

// PASO 4: DETALLES DE REGULARES
Forms\Components\Section::make('Paso 4: Detalles de Talonarios Regulares')
    ->visible(fn($get) => in_array($get('tipo_talonario'), ['regular', 'Preferenciales y Regulares']) && $get('inventario_id'))
    ->schema([
        Forms\Components\Grid::make(3)->schema([
      
TextInput::make('regular_del')
    ->label('Del')
    ->prefixIcon('heroicon-o-arrow-down')
    ->numeric()
    ->required()
    ->minValue(1)
    ->reactive()
    ->default(function () {
        $ultimo = \App\Models\EntregaTalonario::orderByDesc('regular_al')->first();
        return $ultimo ? $ultimo->regular_al + 1 : 1;
    }),

    Forms\Components\TextInput::make('regular_al')
                ->label('Al N°')
                ->prefixIcon('heroicon-o-arrow-up')
                ->numeric()
                ->required()
                ->minValue(1)
               ->gt('regular_del') // ← Validación simple
                ->helperText('Número final'),

            Forms\Components\TextInput::make('rango_inicial_regular')
                ->label('Rango Inicial Facturas')
                ->prefixIcon('heroicon-o-document')
                ->numeric()
                ->required()
                ->default(function () {
                    $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_regular')->first();
                    return $ultimo ? $ultimo->rango_final_regular + 1 : 1;
                })
                ->reactive() // ← AGREGAR ESTO
                ->helperText('Auto-generado'),
        ]),
        
        // Resumen de regulares
        Placeholder::make('resumen_reg')
            ->label('📋 Resumen de Regulares')
            ->content(function ($get) {
                $del = (int) $get('regular_del');
                $al = (int) $get('regular_al');
                $inventarioId = $get('inventario_id');
                $stockDisponible = 0;
                
                if ($inventarioId) {
                    $inventario = \App\Models\InventarioTalonarios::find($inventarioId);
                    $stockDisponible = $inventario?->cantidad_restante_regular ?? 0;
                }
                
                if ($del && $al && $al >= $del) {
                    $cantidad = $al - $del + 1;
                    $mensaje = "**{$cantidad}** talonario(s) regular(es)";
                    $mensaje .= " (Del {$del} al {$al})";
                    
                    if ($stockDisponible > 0) {
                        if ($cantidad > $stockDisponible) {
                            $mensaje .= " ❌ **Stock insuficiente** (Disponible: {$stockDisponible})";
                        } else {
                            $mensaje .= " ✅ **Stock disponible** (Restante: " . ($stockDisponible - $cantidad) . ")";
                        }
                    }
                    
                    return $mensaje;
                }
                return 'Complete los campos "Del" y "Al" para ver el resumen';
            })
            ->extraAttributes(['class' => 'text-sm bg-green-50 p-2 rounded']),
    ])
    ->columns(1)
    ->collapsible(),

            // PASO 5: OBSERVACIONES Y FINALIZACIÓN
            Forms\Components\Section::make('Paso 5: Observaciones y Finalización')
                ->schema([
                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones Adicionales')
                        ->rows(3)
                        ->maxLength(255)
                        ->placeholder('Ingrese cualquier observación adicional sobre la entrega...')
                        ->helperText('Opcional: notas, comentarios o detalles específicos'),
                        
                    Placeholder::make('resumen_final')
                        ->label('🎯 Resumen Final de la Entrega')
                        ->content(function ($get) {
                            $inventarioId = $get('inventario_id');
                            $cajeroId = $get('cajero_id');
                            $tipoTalonario = $get('tipo_talonario');
                            
                            if (!$inventarioId || !$cajeroId || !$tipoTalonario) {
                                return 'Complete todos los pasos anteriores para ver el resumen final';
                            }
                            
                            $inventario = \App\Models\InventarioTalonarios::find($inventarioId);
                            $cajero = \App\Models\Cajero::find($cajeroId);
                            $cajeroPrincipal = $inventario ? \App\Models\Cajero::find($inventario->cajero_id) : null;
                            
                            $resumen = "**ENTREGA PROGRAMADA:**\n\n";
                            $resumen .= "• **Inventario:** #{$inventarioId} - " . ($cajeroPrincipal?->nombre ?? 'N/A') . "\n";
                            $resumen .= "• **Cajero receptor:** " . ($cajero?->nombre ?? 'N/A') . "\n";
                            $resumen .= "• **Tipo de talonario:** {$tipoTalonario}\n";
                            
                            // Agregar detalles específicos
                            if (in_array($tipoTalonario, ['preferencial', 'Preferenciales y Regulares']) && $get('preferencial_del')) {
                                $cantidadPref = ($get('preferencial_al') - $get('preferencial_del') + 1);
                                $resumen .= "• **Preferenciales:** {$cantidadPref} talonarios (Del {$get('preferencial_del')} al {$get('preferencial_al')})\n";
                            }
                            
                            if (in_array($tipoTalonario, ['regular', 'Preferenciales y Regulares']) && $get('regular_del')) {
                                $cantidadReg = ($get('regular_al') - $get('regular_del') + 1);
                                $resumen .= "• **Regulares:** {$cantidadReg} talonarios (Del {$get('regular_del')} al {$get('regular_al')})\n";
                            }
                            
                            $resumen .= "\n✅ **Listo para procesar la entrega**";
                            
                            return $resumen;
                        })
                        ->extraAttributes(['class' => 'text-sm bg-blue-50 p-3 rounded border border-blue-200 whitespace-pre-wrap']),
                ])
                ->columns(1)
                ->collapsible(),
        ]);
}


/**
 * Método auxiliar para generar la información del inventario seleccionado
 */
protected static function getInventarioInfoPlaceholder(): Forms\Components\Placeholder
{
    return Forms\Components\Placeholder::make('info_inventario')
        ->label('')
        ->content(function (callable $get) {
            $inventarioId = $get('inventario_id');
            
            if (!$inventarioId) {
                return new HtmlString('
                    <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Seleccione un inventario para ver sus detalles</p>
                    </div>
                ');
            }
            
            $inventario = \App\Models\InventarioTalonarios::find($inventarioId);
            if (!$inventario) {
                return new HtmlString('
                    <div class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs font-medium text-red-700 dark:text-red-400">Inventario no encontrado</p>
                    </div>
                ');
            }
            
            return new HtmlString(self::generateInventarioInfo($inventario));
        });
}

/**
 * Método auxiliar para generar el mensaje de información del inventario
 */
protected static function generateInventarioInfo($inventario): string
{
    $preferencialStock = $inventario->cantidad_restante_preferencial;
    $regularStock = $inventario->cantidad_restante_regular;
    $totalStock = $preferencialStock + $regularStock;
    
    // Determinar estados visuales
    $prefStatus = self::getStockStatus($preferencialStock);
    $regStatus = self::getStockStatus($regularStock);
    
    return '
    <div class="space-y-3">
        <!-- Contenido en 3 columnas -->
        <div class="flex gap-3">
            <!-- Columna 1: Preferenciales -->
            <div class="flex-1 bg-white dark:bg-gray-800 rounded-lg border ' . $prefStatus['border'] . ' p-3">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 ' . $prefStatus['icon'] . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Preferenciales</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">' . $preferencialStock . '</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ' . $prefStatus['badge'] . '">
                                ' . $prefStatus['text'] . '
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna 2: Regulares -->
            <div class="flex-1 bg-white dark:bg-gray-800 rounded-lg border ' . $regStatus['border'] . ' p-3">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 ' . $regStatus['icon'] . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Regulares</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">' . $regularStock . '</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ' . $regStatus['badge'] . '">
                                ' . $regStatus['text'] . '
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna 3: Total y Estado -->
            <div class="flex-1 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">' . $totalStock . '</p>
                            ' . self::generateAvailabilityBadge($preferencialStock, $regularStock) . '
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mensaje de disponibilidad -->
        ' . self::generateAvailabilityMessage($preferencialStock, $regularStock) . '
=======
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // SELECT REACTIVO ARRIBA DE TODO
                Grid::make(2)->schema([
                    Forms\Components\Select::make('tipo_talonario')
                        ->label('¿Qué tipo de talonario entregará?')
                        ->options([
                            'preferencial' => 'Preferencial',
                            'regular' => 'Regular',
                            'Preferenciales y Regulares' => 'Preferenciales y Regulares',
                        ])
                        ->reactive()
                        ->required()
                        ->searchable()
                        ->native(false)
                        ->placeholder('Selecciona una opción')
                        ->columnSpan(1),

                    Forms\Components\DatePicker::make('fecha_entrega')
                        ->label('Fecha Entrega')
                        ->default(\Carbon\Carbon::now()->toDateString())
                        ->disabled()
                        ->required()
                        ->prefixIcon('heroicon-o-calendar')
                        ->columnSpan(1),
                ]),

                Grid::make(4)->schema([
                    // Sección Datos Principales (también ocupa 2 columnas)
                    Section::make('Datos Principales')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('inventario_id')
                                    ->label('Encargado de Cajer@s')
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

                                Select::make('cajero_id')
                                    ->label('Cajer@s de Patio')
                                    ->prefixIcon('heroicon-o-user')
                                    ->options(function () {
                                        return \App\Models\Cajero::where('tipo_cajero', 'secundario')
                                            ->get()
                                            ->mapWithKeys(function ($cajero) {
                                                $fullName = "{$cajero->nombre} {$cajero->apellido_paterno} {$cajero->apellido_materno}";
                                                return [$cajero->id => $fullName];
                                            });
                                    })
                                    ->searchable()
                                    ->required(),


                            ]),
                        ])
                        ->columnSpan(2), // <-- clave para mostrar al lado

                    // Sección Información (ocupa 2 columnas de las 4 disponibles)
                    Section::make('Resumen Rápido')
                        ->schema(function () {
                            $preferencial = \App\Models\InventarioTalonarios::where('estado_preferencial', 1)->sum('cantidad_restante_preferencial');
                            $regular = \App\Models\InventarioTalonarios::where('estado_regular', 1)->sum('cantidad_restante_regular');

                            $nDosificacion = \App\Models\InventarioTalonarios::where(function ($query) {
                                $query->where('estado_preferencial', 1)
                                    ->orWhere('estado_regular', 1);
                            })->value('n_dosificacion') ?? 'No disponible';

                            // Estados
                            $estadoPreferencial = match (true) {
                                $preferencial <= 50 => ['❗ Crítico', 'text-red-600 font-bold'],
                                $preferencial <= 100 => ['⚠️ Bajo', 'text-yellow-600 font-semibold'],
                                default => ['✅ Asignable', 'text-green-600 font-semibold'],
                            };

                            $estadoRegular = match (true) {
                                $regular <= 50 => ['❗ Crítico', 'text-red-600 font-bold'],
                                $regular <= 100 => ['⚠️ Bajo', 'text-yellow-600 font-semibold'],
                                default => ['✅ Asignable', 'text-green-600 font-semibold'],
                            };

                            return [
                                Grid::make(3)->schema([
                                    Placeholder::make('n_dosificacion')
                                        ->label('🧾 N° Dosificación')
                                        ->content($nDosificacion)
                                        ->extraAttributes(['class' => 'text-blue-600 font-semibold']),

                                    Placeholder::make('estado_preferencial')
                                        ->label('🎟️ Preferencial')
                                        ->content("{$preferencial} → {$estadoPreferencial[0]}")
                                        ->extraAttributes(['class' => $estadoPreferencial[1]]),

                                    Placeholder::make('estado_regular')
                                        ->label('🎫 Regular')
                                        ->content("{$regular} → {$estadoRegular[0]}")
                                        ->extraAttributes(['class' => $estadoRegular[1]]),
                                ]),
                            ];
                        })
                        ->collapsible()
                        ->columnSpan(2),
                ]),

                // SECCIÓN PREFERENCIALES
                Forms\Components\Section::make('Preferenciales')
                    ->visible(fn($get) => in_array($get('tipo_talonario'), ['preferencial', 'Preferenciales y Regulares']))
                    ->schema([
                        Forms\Components\Grid::make(4)->schema([
                            Forms\Components\TextInput::make('preferencial_del')
                                ->label('Del')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric()
                                ->reactive(),

                            Forms\Components\TextInput::make('preferencial_al')
                                ->label('Al')
                                ->prefixIcon('heroicon-o-arrow-up')
                                ->numeric()
                                ->reactive()
                                ->rule(function (callable $get) {
                                    $del = (int) $get('preferencial_del');
                                    return function ($attribute, $value, $fail) use ($del) {
                                        if ($del && $value < $del) {
                                            $fail('El campo "Al" no puede ser menor que el campo "Del".');
                                        }
                                    };
                                }),


                            TextInput::make('cantidad_preferenciales')
                                ->label('Cantidad Preferenciales')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->reactive()
                                ->rules(function () {
                                    // Sumar todos los talonarios preferenciales disponibles de registros activos
                                    $cantidadDisponible = DB::table('inventario_talonarios')
                                        ->where('estado_preferencial', 1)
                                        ->sum('cantidad_restante_preferencial');

                                    return [
                                        function ($attribute, $value, $fail) use ($cantidadDisponible) {
                                            if ((int)$value > $cantidadDisponible) {
                                                $fail("No hay suficientes talonarios preferenciales en inventario. Disponibles: $cantidadDisponible");
                                            }
                                        },
                                    ];
                                })

                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $cantidad = (int) $state;
                                    $del = (int) $get('preferencial_del');

                                    if ($del && $cantidad > 0) {
                                        $set('preferencial_al', $del + $cantidad - 1);
                                    }
                                }),



                            Forms\Components\TextInput::make('rango_inicial_preferencial')
                                ->label('Rango Inicial')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric()
                                ->default(function () {
                                    $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_preferencial')->first();
                                    return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                                }),
                        ]),
                    ]),

                // SECCIÓN REGULARES
                Forms\Components\Section::make('Regulares')
                    ->visible(fn($get) => in_array($get('tipo_talonario'), ['regular', 'Preferenciales y Regulares']))
                    ->schema([
                        Forms\Components\Grid::make(4)->schema([

                            Forms\Components\TextInput::make('regular_del')
                                ->label('Del')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric()
                                ->reactive(),


                            Forms\Components\TextInput::make('regular_al')
                                ->label('Al')
                                ->prefixIcon('heroicon-o-arrow-up')
                                ->numeric()
                                ->reactive()
                                ->rule(function (callable $get) {
                                    $del = (int) $get('regular_del');
                                    return function ($attribute, $value, $fail) use ($del) {
                                        if ($del && $value < $del) {
                                            $fail('El campo "Al" no puede ser menor que el campo "Del".');
                                        }
                                    };
                                }),

                            TextInput::make('cantidad_regulares')
                                ->label('Cantidad Regulares')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->numeric()
                                ->disabled()              // ❌ No editable
                                ->dehydrated(true)        // ✅ Se guarda en la base de datos
                                ->reactive()
                                ->rules(function () {
                                    $cantidadDisponible = DB::table('inventario_talonarios')
                                        ->where('estado_regular', 1)
                                        ->sum('cantidad_restante_regular');

                                    return [
                                        function ($attribute, $value, $fail) use ($cantidadDisponible) {
                                            if ((int)$value > $cantidadDisponible) {
                                                $fail("No hay suficientes talonarios regulares en inventario. Disponibles: $cantidadDisponible");
                                            }
                                        },
                                    ];
                                })

                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $cantidad = (int) $state;
                                    $del = (int) $get('regular_del');

                                    if ($del && $cantidad > 0) {
                                        $set('regular_al', $del + $cantidad - 1);
                                    }
                                }),

                            Forms\Components\TextInput::make('rango_inicial_regular')
                                ->label('Rango Inicial')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric()
                                ->default(function () {
                                    $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_regular')->first();
                                    return $ultimo ? $ultimo->rango_final_regular + 1 : 1;
                                }),
                        ]),
                    ]),

                Forms\Components\Section::make('Entrega y Observaciones')
                    ->schema([
                        Forms\Components\Grid::make(1)->schema([
                            Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones')
                                ->rows(3)
                                ->maxLength(255)
                                ->nullable(),
                        ]),
                    ]),
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

    function cleanUtf8($string)
    {
        // Elimina caracteres no válidos
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = preg_replace('//u', '', $string);
        return $string;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inventario_id')
                    ->label('Encargad@ de Cajer@s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        $inventario = \App\Models\InventarioTalonarios::find($record->inventario_id);

                        if ($inventario && $inventario->cajero_id) {
                            $cajero = \App\Models\Cajero::find($inventario->cajero_id);
                            if ($cajero) {
                                return $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                            }
                        }
                        return 'No disponible';
                    }),

                Tables\Columns\TextColumn::make('cajero_id')
                    ->label('Cajer@s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        $cajero = \App\Models\Cajero::find($record->cajero_id);
                        return $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                    }),

                Tables\Columns\TextColumn::make('resumen_preferenciales')
                    ->label('🎟️ Preferenciales')
                    ->html()
                    ->getStateUsing(function ($record) {
                        // Debug 1: Verificar si el campo existe y mostrar todos los campos disponibles
                        if (!isset($record->estado_preferencial)) {
                            $availableFields = implode(', ', array_keys((array)$record));
                            return "<div style='color:red; font-weight:bold;'>ERROR: El campo 'estado_preferencial' no existe en este registro.</div>"
                                . "<div>Campos disponibles: {$availableFields}</div>"
                                . "<div>Valores recibidos: " . json_encode($record) . "</div>";
                        }

                        // Debug 2: Mostrar el valor crudo del campo
                        $rawEstado = $record->getRawOriginal('estado_preferencial') ?? 'null';

                        $total = $record->cantidad_preferenciales ?? 1;
                        $restante = $record->cantidad_restante_preferencial ?? 0;
                        $porcentaje = $total > 0 ? round(($restante / $total) * 100) : 0;
                        $colorPorcentaje = $porcentaje < 30 ? 'red' : ($porcentaje < 60 ? 'orange' : 'green');

                        // Debug 3: Forzar diferentes valores para prueba
                        // $rawEstado = 2; // <-- Descomentar para probar manualmente

                        $estadoValor = (int) $rawEstado;

                        $estadoColor = match ($estadoValor) {
                            1 => 'green',     // Asignable
                            2 => 'blue',      // En espera
                            default => 'red'  // Asignado (0 o cualquier otro valor)
                        };

                        $estadoTexto = match ($estadoValor) {
                            1 => 'Asignable',
                            2 => 'En espera',
                            default => 'Asignado'
                        };

                        return "
<span>🔢 <strong>Cantidad:</strong> {$total}</span><br>
<span>🔁 <strong>Rango Original:</strong> {$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}</span><br>
<span>📅 <strong>Del - Al:</strong> {$record->preferencial_del} - {$record->preferencial_al}</span><br>
<span>📉 <strong>Restan:</strong> <span style='color:{$colorPorcentaje}'>{$restante} ({$porcentaje}%)</span></span><br>
<span>💰 <strong>Total Bs.:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_preferencial, 2, '.', ',') . "</span><br>
<span>📌 <strong>Estado:</strong> <span style='color:{$estadoColor}'>{$estadoTexto}</span></span>
<br>
<div style='color:gray; font-size:0.8em;'>
</div>";
                    }),


                ProgressBar::make('preferenciales_progress_bar')
                    ->getStateUsing(function ($record) {
                        $total = $record->cantidad_preferenciales ?? 1;
                        $restante = $record->cantidad_restante_preferencial ?? 0;
                        if ($total == 0) $total = 1;
                        $porcentaje = round(($restante / $total) * 100);
                        return [
                            'total' => 100,
                            'progress' => $porcentaje,
                        ];
                    })
                    ->label('% Restante Preferenciales'),

                // Para regulares también:
                Tables\Columns\TextColumn::make('resumen_regulares')
                    ->label('🎫 Regulares')
                    ->html()
                    ->getStateUsing(function ($record) {
                        // Debug 1: Verificar si el campo existe y mostrar todos los campos disponibles
                        if (!isset($record->estado_regular)) {
                            $availableFields = implode(', ', array_keys((array)$record));
                            return "<div style='color:red; font-weight:bold;'>ERROR: El campo 'estado_regular' no existe en este registro.</div>"
                                . "<div>Campos disponibles: {$availableFields}</div>"
                                . "<div>Valores recibidos: " . json_encode($record) . "</div>";
                        }

                        // Debug 2: Mostrar el valor crudo del campo
                        $rawEstado = $record->getRawOriginal('estado_regular') ?? 'null';

                        $total = $record->cantidad_regulares ?? 1;
                        $restante = $record->cantidad_restante_regular ?? 0;
                        $porcentaje = $total > 0 ? round(($restante / $total) * 100) : 0;
                        $colorPorcentaje = $porcentaje < 30 ? 'red' : ($porcentaje < 60 ? 'orange' : 'green');

                        // Debug 3: Forzar diferentes valores para prueba
                        // $rawEstado = 2; // <-- Descomentar para probar manualmente

                        $estadoValor = (int) $rawEstado;

                        $estadoColor = match ($estadoValor) {
                            1 => 'green',     // Asignable
                            2 => 'blue',      // En espera
                            default => 'red'  // Asignado (0 o cualquier otro valor)
                        };

                        $estadoTexto = match ($estadoValor) {
                            1 => 'Asignable',
                            2 => 'En espera',
                            default => 'Asignado'
                        };

                        return "
<span>🔢 <strong>Cantidad:</strong> {$total}</span><br>
<span>🔁 <strong>Rango Original:</strong> {$record->rango_inicial_regular} - {$record->rango_final_regular}</span><br>
<span>📅 <strong>Del - Al:</strong> {$record->regular_del} - {$record->regular_al}</span><br>
<span>📉 <strong>Restan:</strong> <span style='color:{$colorPorcentaje}'>{$restante} ({$porcentaje}%)</span></span><br>
<span>💰 <strong>Total Bs.:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_regular, 2, '.', ',') . "</span><br>
<span>📌 <strong>Estado:</strong> <span style='color:{$estadoColor}'>{$estadoTexto}</span></span>
<br>
<div style='color:gray; font-size:0.8em;'>
</div>";
                    }),
                ProgressBar::make('regulares_progress_bar')
                    ->getStateUsing(function ($record) {
                        $total = $record->cantidad_regulares ?? 1;
                        $restante = $record->cantidad_restante_regular ?? 0;
                        if ($total == 0) $total = 1;
                        $porcentaje = round(($restante / $total) * 100);
                        return [
                            'total' => 100,
                            'progress' => $porcentaje,
                        ];
                    })
                    ->label('% Restante Regulares'),
            ])
            ->filters([
                //
            ])


            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('generar_pdf')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-document')
                    ->color('success')
                    ->action(function ($record) {
                        $cajero = \App\Models\Cajero::find($record->cajero_id);
                        $nombreCompleto = $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                        $ci = $cajero ? $cajero->ci : 'No disponible';

                        $tipo_talonario = $record->tipo_talonario;
                        $filasTabla = '';

                        if (in_array($tipo_talonario, ['preferencial', 'Preferenciales y Regulares']) && $record->cantidad_preferenciales > 0) {
                            $rangoTicketsInicial = $record->preferencial_del ?? $record->rango_inicial_preferencial;
                            $rangoTicketsFinal = $record->preferencial_al ?? ($record->rango_inicial_preferencial + ($record->cantidad_preferenciales * 50) - 1);

                            $rangoFacturasInicial = $record->rango_inicial_preferencial;
                            $rangoFacturasFinal = $record->rango_final_preferencial ?? ($record->rango_inicial_preferencial + $record->cantidad_preferenciales - 1);

                            $filasTabla .= '
                <tr>
                    <td>PREFERENCIAL</td>
                    <td>' . number_format($rangoTicketsInicial, 0, '', ',') . '</td>
                    <td>' . number_format($rangoTicketsFinal, 0, '', ',') . '</td>
                    <td>' . $record->cantidad_preferenciales . ' talonarios</td>
                    <td>' . number_format($rangoFacturasInicial, 0, '', ',') . ' - ' . number_format($rangoFacturasFinal, 0, '', ',') . '</td>
                </tr>';
                        }

                        if (in_array($tipo_talonario, ['regular', 'Preferenciales y Regulares']) && $record->cantidad_regulares > 0) {
                            $rangoTicketsInicial = $record->regular_del ?? $record->rango_inicial_regular;
                            $rangoTicketsFinal = $record->regular_al ?? ($record->rango_inicial_regular + ($record->cantidad_regulares * 50) - 1);

                            $rangoFacturasInicial = $record->rango_inicial_regular;
                            $rangoFacturasFinal = $record->rango_final_regular ?? ($record->rango_inicial_regular + $record->cantidad_regulares - 1);

                            $filasTabla .= '
                <tr>
                    <td>REGULAR</td>
                    <td>' . number_format($rangoTicketsInicial, 0, '', ',') . '</td>
                    <td>' . number_format($rangoTicketsFinal, 0, '', ',') . '</td>
                    <td>' . $record->cantidad_regulares . ' talonarios</td>
                    <td>' . number_format($rangoFacturasInicial, 0, '', ',') . ' - ' . number_format($rangoFacturasFinal, 0, '', ',') . '</td>
                </tr>';
                        }

                        $fechaActual = \Carbon\Carbon::now()->locale('es')->isoFormat('D [días del mes de] MMMM [del año] YYYY');

                        $encabezadoPath = public_path('img/Galeria/encabezado.png');
                        $piePath = public_path('img/Galeria/pie de pagina.png');
                        $encabezadoBase64 = file_exists($encabezadoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($encabezadoPath)) : '';
                        $pieBase64 = file_exists($piePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($piePath)) : '';

                        $html = '
            <html>
            <head>
                <style>
                    @page { size: letter; margin: 1in; }
                    body {
                        font-family: DejaVu Sans, sans-serif;
                        font-size: 12px;
                        margin: 0;
                        padding: 0;
                        position: relative;
                        min-height: 100vh;
                        padding-bottom: 120px;
                    }
                    .encabezado { text-align: center; margin-bottom: 20px; }
                    .encabezado img { max-width: 100%; height: auto; }
                    h2 {
                        text-align: center;
                        text-decoration: underline;
                        margin-bottom: 20px;
                    }
                    table {
                        width: 90%;
                        border-collapse: collapse;
                        margin: 15px auto;
                        font-size: 10px;
                    }
                    td, th {
                        border: 1px solid #000;
                        padding: 6px;
                        text-align: center;
                        vertical-align: middle;
                    }
                    th {
                        background-color: #f0f0f0;
                        font-weight: bold;
                    }
                    .firmas-finales {
                        margin-top: 60px;
                        width: 60%;
                        margin-left: auto;
                        margin-right: auto;
                        clear: both;
                    }
                    .firma-izquierda, .firma-derecha {
                        width: 45%;
                        text-align: center;
                    }
                    .firma-izquierda { float: left; }
                    .firma-derecha { float: right; }
                    .pie-pagina {
                        margin-top: 80px;
                        text-align: center;
                        clear: both;
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        right: 0;
                    }
                    .fecha-generacion {
                        position: absolute;
                        top: -20px;
                        right: 10px;
                        font-size: 8px;
                        color: #333;
                        font-weight: bold;
                        background-color: rgba(255, 255, 255, 0.9);
                        padding: 3px 8px;
                        border-radius: 3px;
                        border: 1px solid #ccc;
                    }
                    p {
                        text-align: justify;
                        line-height: 1.4;
                        margin: 15px 0;
                    }
                </style>
            </head>
            <body>
                <div class="encabezado">' .
                            ($encabezadoBase64 ? '<img src="' . $encabezadoBase64 . '">' : '<h3>EMPRESA - ENCABEZADO</h3>') . '
                </div>

                <h2>ACTA DE ENTREGA DE TALONARIOS</h2>

                <p>Mediante la presente Acta, se efectúa la entrega de talonarios <strong>' . strtoupper($tipo_talonario) . '</strong> a la siguiente persona:</p>

                <table>
                    <tr><th>N°</th><th>CAJERO (A)</th><th>C.I.</th></tr>
                    <tr>
                        <td>1</td>
                        <td>' . htmlspecialchars($nombreCompleto) . '</td>
                        <td>' . htmlspecialchars($ci) . '</td>
                    </tr>
                </table>

                <p>Al respecto, se aclara que los mismos se harán responsables por la asignación y recaudo de las FACTURAS PRE VALORADAS, siendo el rango de tickets y facturas de acuerdo al siguiente detalle:</p>

                <table>
                    <tr>
                        <th rowspan="2">TIPO DE TICKET</th>
                        <th colspan="2">RANGO DE TICKETS</th>
                        <th rowspan="2">CANTIDAD</th>
                        <th rowspan="2">RANGO DE FACTURAS</th>
                    </tr>
                    <tr>
                        <th>DESDE</th>
                        <th>HASTA</th>
                    </tr>
                    ' . $filasTabla . '
                </table>

                <p><strong>Nota:</strong> Cada talonario contiene 50 tickets.</p>

                <p>El incumplimiento, si corresponde, será pasivo a sanciones administrativas. Dando el asentimiento al contenido de la presente Acta de Corresponsabilidad, es firmado en la ciudad de El Alto, a los ' . $fechaActual . '.</p>

           <table style="width: 100%; border-collapse: collapse; border: none;">
  <tr>
    <!-- Firma Izquierda (centrada en su columna) -->
    <td style="width: 50%; padding: 0; border: none; vertical-align: top;">
      <div style="margin-top: 15px; text-align: center; margin-left: 20px;">
        <p style="margin: 2px 0; line-height: 1.2;">_________________________</p>
        <p style="margin: 2px 0; line-height: 1.2;"><strong>Firma del Cajero</strong></p>
        <p style="margin: 2px 0; line-height: 1.2;">' . htmlspecialchars($nombreCompleto) . '</p>
        <p style="margin: 2px 0; line-height: 1.2;">C.I.: ' . htmlspecialchars($ci) . '</p>
      </div>
    </td>

    <!-- Firma Derecha (alineada al borde derecho) -->
    <td style="width: 50%; padding: 0; border: none; vertical-align: top; text-align: right;">
      <div style="margin-top: 15px; display: inline-block; text-align: center; margin-right: 20px;">
        <p style="margin: 2px 0; line-height: 1.2;">_________________________</p>
        <p style="margin: 2px 0; line-height: 1.2;"><strong>Encargado de Cajeros</strong></p>
        <p style="margin: 2px 0; line-height: 1.2;"><strong>Firma y Sello</strong></p>
      </div>
    </td>
  </tr>
</table>

             <div class="pie-pagina">

        PDF generado el: ' . \Carbon\Carbon::now()->format('d/m/Y H:i:s') . '

        ' . ($pieBase64 ? '<img src="' . $pieBase64 . '">' : 'Dirección de la empresa | Teléfono | Email') . '
>>>>>>> 177fd53dbdc811c93d77d3caa4bd2aef498d1e4f
    </div>
    ';
}

<<<<<<< HEAD
/**
 * Determinar el estado completo del stock
 */
protected static function getStockStatus(int $quantity): array
{
    if ($quantity === 0) {
        return [
            'text' => 'Agotado',
            'badge' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            'icon' => 'text-red-500 dark:text-red-400',
            'border' => 'border-red-200 dark:border-red-700'
        ];
    }
    
    if ($quantity <= 5) {
        return [
            'text' => 'Crítico',
            'badge' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'icon' => 'text-yellow-500 dark:text-yellow-400',
            'border' => 'border-yellow-200 dark:border-yellow-700'
        ];
    }
    
    if ($quantity <= 10) {
        return [
            'text' => 'Bajo',
            'badge' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'icon' => 'text-blue-500 dark:text-blue-400',
            'border' => 'border-blue-200 dark:border-blue-700'
        ];
    }
    
    return [
        'text' => 'OK',
        'badge' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'icon' => 'text-green-500 dark:text-green-400',
        'border' => 'border-green-200 dark:border-green-700'
    ];
}

/**
 * Generar badge de disponibilidad general
 */
protected static function generateAvailabilityBadge(int $pref, int $reg): string
{
    if ($pref === 0 && $reg === 0) {
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
            Sin Stock
        </span>';
    } elseif ($pref === 0 || $reg === 0) {
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
            Parcial
        </span>';
    } else {
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
            Disponible
        </span>';
    }
}

/**
 * Generar mensaje de disponibilidad
 */
protected static function generateAvailabilityMessage(int $pref, int $reg): string
{
    if ($pref === 0 && $reg === 0) {
        return '
        <div class="bg-red-50 border-l-4 border-red-400 p-2 rounded text-xs dark:bg-red-900/20 dark:border-red-600">
            <p class="font-medium text-red-800 dark:text-red-300">⚠️ Sin talonarios disponibles</p>
        </div>';
    } elseif ($pref === 0) {
        return '
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-2 rounded text-xs dark:bg-yellow-900/20 dark:border-yellow-600">
            <p class="font-medium text-yellow-800 dark:text-yellow-300">ℹ️ Solo regulares disponibles</p>
        </div>';
    } elseif ($reg === 0) {
        return '
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-2 rounded text-xs dark:bg-yellow-900/20 dark:border-yellow-600">
            <p class="font-medium text-yellow-800 dark:text-yellow-300">ℹ️ Solo preferenciales disponibles</p>
        </div>';
    } else {
        return '
        <div class="bg-green-50 border-l-4 border-green-400 p-2 rounded text-xs dark:bg-green-900/20 dark:border-green-600">
            <p class="font-medium text-green-800 dark:text-green-300">✓ Ambos tipos disponibles</p>
        </div>';
    }
}
    // El resto del código (table, relations, pages) permanece igual...
    // ... (mantener el mismo código de table, filters, actions, etc.)

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inventario_id')
                    ->label('Encargad@ de Cajer@s')
                    ->getStateUsing(function ($record) {
                        $inventario = \App\Models\InventarioTalonarios::find($record->inventario_id);
                        if ($inventario && $inventario->cajero_id) {
                            $cajero = \App\Models\Cajero::find($inventario->cajero_id);
                            if ($cajero) {
                                return $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                            }
                        }
                        return 'No disponible';
                    }),

                Tables\Columns\TextColumn::make('cajero_id')
                    ->label('Cajer@s')
                    ->getStateUsing(function ($record) {
                        $cajero = \App\Models\Cajero::find($record->cajero_id);
                        return $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                    }),

                Tables\Columns\TextColumn::make('preferencial_del')
                    ->label('Pref. Del')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('preferencial_al')
                    ->label('Pref. Al')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_inicial_preferencial')
                    ->label('Rango Inicial Pref.')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('regular_del')
                    ->label('Reg. Del')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('regular_al')
                    ->label('Reg. Al')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_inicial_regular')
                    ->label('Rango Inicial Reg.')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tipo_talonario')
                    ->label('Tipo'),

                Tables\Columns\TextColumn::make('fecha_entrega')
                    ->label('Fecha')
                    ->date(),

                Tables\Columns\TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cajero_id')
                    ->label('Cajero')
                    ->options(
                        Cajero::where('tipo_cajero', 'secundario')
                            ->get()
                            ->mapWithKeys(fn($cajero) => [
                                $cajero->id => ($cajero->nombre ?? '') . ' ' . ($cajero->apellido_paterno ?? '') . ' ' . ($cajero->apellido_materno ?? '')
                            ])
                            ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('inventario_id')
                    ->label('Encargado')
                    ->options(
                        Cajero::where('tipo_cajero', 'principal')
                            ->get()
                            ->mapWithKeys(fn($cajero) => [
                                $cajero->id => ($cajero->nombre ?? '') . ' ' . ($cajero->apellido_paterno ?? '') . ' ' . ($cajero->apellido_materno ?? '')
                            ])
                            ->toArray()
                    ),

                Tables\Filters\TernaryFilter::make('observaciones')
                    ->label('Tiene Observaciones')
                    ->trueLabel('Sí')
                    ->falseLabel('No')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('observaciones')->where('observaciones', '!=', ''),
                        false: fn($query) => $query->whereNull('observaciones')->orWhere('observaciones', '')
                    ),

                Tables\Filters\SelectFilter::make('tipo_talonario')
                    ->label('Tipo de Talonario')
                    ->options([
                        'preferencial' => 'Preferencial',
                        'regular' => 'Regular',
                        'Preferenciales y Regulares' => 'Preferenciales y Regulares',
                    ]),

                Tables\Filters\Filter::make('fecha_entrega')
                    ->label('Rango de Fecha Entrega')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('from')->label('Desde'),
                            Forms\Components\DatePicker::make('until')->label('Hasta'),
                        ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($query) => $query->whereDate('fecha_entrega', '>=', $data['from']))
                            ->when($data['until'], fn($query) => $query->whereDate('fecha_entrega', '<=', $data['until']));
                    }),
            ])
            ->filtersFormColumns(2)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('warning'),

                    Action::make('generar_pdf')
                        ->label('Generar PDF')
                        ->icon('heroicon-o-document')
                        ->color('success')
                        ->action(function ($record) {
                            $cajero = \App\Models\Cajero::find($record->cajero_id);
                            $nombreCompleto = $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                            $ci = $cajero ? $cajero->ci : 'No disponible';

                            $tipo_talonario = $record->tipo_talonario;
                            $filasTabla = '';

                            if (in_array($tipo_talonario, ['preferencial', 'Preferenciales y Regulares']) && $record->preferencial_del) {
                                $rangoTicketsInicial = $record->preferencial_del;
                                $rangoTicketsFinal = $record->preferencial_al;
                                $cantidad = $record->preferencial_al - $record->preferencial_del + 1;

                                $filasTabla .= '
                                <tr>
                                    <td>PREFERENCIAL</td>
                                    <td>' . number_format($rangoTicketsInicial, 0, '', ',') . '</td>
                                    <td>' . number_format($rangoTicketsFinal, 0, '', ',') . '</td>
                                    <td>' . $cantidad . ' tickets</td>
                                    <td>' . number_format($record->rango_inicial_preferencial, 0, '', ',') . '</td>
                                </tr>';
                            }

                            if (in_array($tipo_talonario, ['regular', 'Preferenciales y Regulares']) && $record->regular_del) {
                                $rangoTicketsInicial = $record->regular_del;
                                $rangoTicketsFinal = $record->regular_al;
                                $cantidad = $record->regular_al - $record->regular_del + 1;

                                $filasTabla .= '
                                <tr>
                                    <td>REGULAR</td>
                                    <td>' . number_format($rangoTicketsInicial, 0, '', ',') . '</td>
                                    <td>' . number_format($rangoTicketsFinal, 0, '', ',') . '</td>
                                    <td>' . $cantidad . ' tickets</td>
                                    <td>' . number_format($record->rango_inicial_regular, 0, '', ',') . '</td>
                                </tr>';
                            }

                            $fechaActual = \Carbon\Carbon::now()->locale('es')->isoFormat('D [días del mes de] MMMM [del año] YYYY');

                            $encabezadoPath = public_path('img/Galeria/encabezado.png');
                            $piePath = public_path('img/Galeria/pie de pagina.png');
                            $encabezadoBase64 = file_exists($encabezadoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($encabezadoPath)) : '';
                            $pieBase64 = file_exists($piePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($piePath)) : '';

                            $html = '
                            <html>
                            <head>
                                <style>
                                    @page { size: letter; margin: 1in; }
                                    body {
                                        font-family: DejaVu Sans, sans-serif;
                                        font-size: 12px;
                                        margin: 0;
                                        padding: 0;
                                        position: relative;
                                        min-height: 100vh;
                                        padding-bottom: 120px;
                                    }
                                    .encabezado { text-align: center; margin-bottom: 20px; }
                                    .encabezado img { max-width: 100%; height: auto; }
                                    h2 {
                                        text-align: center;
                                        text-decoration: underline;
                                        margin-bottom: 20px;
                                    }
                                    table {
                                        width: 90%;
                                        border-collapse: collapse;
                                        margin: 15px auto;
                                        font-size: 10px;
                                    }
                                    td, th {
                                        border: 1px solid #000;
                                        padding: 6px;
                                        text-align: center;
                                        vertical-align: middle;
                                    }
                                    th {
                                        background-color: #f0f0f0;
                                        font-weight: bold;
                                    }
                                    p {
                                        text-align: justify;
                                        line-height: 1.4;
                                        margin: 15px 0;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="encabezado">' .
                                    ($encabezadoBase64 ? '<img src="' . $encabezadoBase64 . '">' : '<h3>EMPRESA - ENCABEZADO</h3>') . '
                                </div>

                                <h2>ACTA DE ENTREGA DE TALONARIOS</h2>

                                <p>Mediante la presente Acta, se efectúa la entrega de talonarios <strong>' . strtoupper($tipo_talonario) . '</strong> a la siguiente persona:</p>

                                <table>
                                    <tr><th>N°</th><th>CAJERO (A)</th><th>C.I.</th></tr>
                                    <tr>
                                        <td>1</td>
                                        <td>' . htmlspecialchars($nombreCompleto) . '</td>
                                        <td>' . htmlspecialchars($ci) . '</td>
                                    </tr>
                                </table>

                                <p>Al respecto, se aclara que los mismos se harán responsables por la asignación y recaudo de las FACTURAS PRE VALORADAS, siendo el rango de tickets y facturas de acuerdo al siguiente detalle:</p>

                                <table>
                                    <tr>
                                        <th rowspan="2">TIPO DE TICKET</th>
                                        <th colspan="2">RANGO DE TICKETS</th>
                                        <th rowspan="2">CANTIDAD</th>
                                        <th rowspan="2">RANGO DE FACTURAS</th>
                                    </tr>
                                    <tr>
                                        <th>DESDE</th>
                                        <th>HASTA</th>
                                    </tr>
                                    ' . $filasTabla . '
                                </table>

                                <p>El incumplimiento, si corresponde, será pasivo a sanciones administrativas. Dando el asentimiento al contenido de la presente Acta de Corresponsabilidad, es firmado en la ciudad de El Alto, a los ' . $fechaActual . '.</p>

                                <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 60px;">
                                    <tr>
                                        <td style="width: 50%; padding: 0; border: none; vertical-align: top;">
                                            <div style="text-align: center;">
                                                <p style="margin: 2px 0; line-height: 1.2;">_________________________</p>
                                                <p style="margin: 2px 0; line-height: 1.2;"><strong>Firma del Cajero</strong></p>
                                                <p style="margin: 2px 0; line-height: 1.2;">'.htmlspecialchars($nombreCompleto).'</p>
                                                <p style="margin: 2px 0; line-height: 1.2;">C.I.: '.htmlspecialchars($ci).'</p>
                                            </div>
                                        </td>
                                        
                                        <td style="width: 50%; padding: 0; border: none; vertical-align: top; text-align: right;">
                                            <div style="display: inline-block; text-align: center; margin-right: 20px;">
                                                <p style="margin: 2px 0; line-height: 1.2;">_________________________</p>
                                                <p style="margin: 2px 0; line-height: 1.2;"><strong>Encargado de Cajeros</strong></p>
                                                <p style="margin: 2px 0; line-height: 1.2;"><strong>Firma y Sello</strong></p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <div style="margin-top: 80px; text-align: center;">
                                    PDF generado el: '.\Carbon\Carbon::now()->format('d/m/Y H:i:s').'
                                    '.($pieBase64 ? '<img src="' . $pieBase64 . '">' : 'Dirección de la empresa | Teléfono | Email').'
                                </div>
                            </body>
                            </html>';

                            $pdf = Pdf::loadHTML($html)
                                ->setPaper('letter', 'portrait')
                                ->setOptions([
                                    'defaultFont' => 'DejaVu Sans',
                                    'isRemoteEnabled' => true,
                                    'isHtml5ParserEnabled' => true,
                                ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->stream();
                            }, 'acta_entrega_talonarios_' . $record->id . '.pdf');
                        }),

                    Tables\Actions\Action::make('ver_detalles')
                        ->label('Ver Detalles')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalContent(fn($record) => view('filament.resources.entrega-talonario.modal-detalles', compact('record')))
                        ->modalWidth('5xl'),
                ])
                    ->icon('heroicon-o-bars-3')
                    ->label('Opciones'),
            ])
            ->headerActions([
                CreateAction::make(),

                Tables\Actions\Action::make('descargar_reporte_pdf')
                    ->label('Descargar Reporte PDF')
                    ->icon('heroicon-o-printer')
                    ->action(function () {
                        Notification::make()
                            ->title('Funcionalidad en desarrollo')
                            ->body('La descarga de reportes estará disponible próximamente.')
                            ->info()
                            ->send();
                    }),

                Tables\Actions\Action::make('estadisticas')
                    ->label('Estadísticas')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->action(function () {
                        $totalEntregas = EntregaTalonario::count();

                        Notification::make()
                            ->title('Estadísticas Generales')
                            ->body("📊 Total Entregas: {$totalEntregas}")
                            ->info()
                            ->persistent()
                            ->send();
                    }),
            ])
=======
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                            ->setPaper('letter', 'portrait')
                            ->setOptions([
                                'defaultFont' => 'DejaVu Sans',
                                'isRemoteEnabled' => true,
                                'isHtml5ParserEnabled' => true,
                            ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'acta_entrega_talonarios_' . $record->id . '.pdf');
                    }),
            ])


            ->headerActions([
                CreateAction::make(),
            ])

>>>>>>> 177fd53dbdc811c93d77d3caa4bd2aef498d1e4f
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
