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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ActionGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\HtmlString;
use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

class EntregaTalonariosAnfitrionResource extends Resource
{
    protected static ?string $model = EntregaTalonariosAnfitrion::class;
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationLabel = 'Inventario del Anfitrión';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // PASO 1: SELECCIÓN DE INVENTARIO Y ANFITRIÓN CON LÓGICA FIFO
                Section::make('Paso 1: Selección de Inventario y Anfitrión')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('entrega_talonario_id')
                                ->label('Seleccione el Inventario del Cajero')
                                ->prefixIcon('heroicon-o-archive-box')
                                ->options(function () {
                                    return \App\Models\EntregaTalonario::where(function ($query) {
                                        $query->where('cantidad_restante_preferencial', '>', 0)
                                              ->orWhere('cantidad_restante_regular', '>', 0);
                                    })
                                    ->where('estado', 'activo')
                                    ->orderBy('created_at', 'asc')
                                    ->get()
                                    ->mapWithKeys(function ($entrega) {
                                        $cajero = \App\Models\Cajero::find($entrega->cajero_id);
                                        $nombreCajero = $cajero ? 
                                            "{$cajero->nombre} {$cajero->apellido_paterno}" : 
                                            'Sin asignar';
                                        
                                        $info = "{$nombreCajero}";
                                        if ($entrega->cantidad_restante_preferencial > 0) {
                                            $info .= " | Pref: {$entrega->cantidad_restante_preferencial}";
                                        }
                                        if ($entrega->cantidad_restante_regular > 0) {
                                            $info .= " | Reg: {$entrega->cantidad_restante_regular}";
                                        }
                                        
                                        return [$entrega->id => $info];
                                    });
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $entrega = \App\Models\EntregaTalonario::find($state);
                                        if ($entrega) {
                                            $set('tipo_talonarios', null);
                                            $set('preferencial_Del', null);
                                            $set('preferencial_Al', null);
                                            $set('regular_Del', null);
                                            $set('regular_Al', null);
                                        }
                                    }
                                }),

                            Select::make('anfitrion_id')
                                ->label('Seleccione el Anfitrión')
                                ->prefixIcon('heroicon-o-user')
                                ->relationship('anfitrion', 'nombre')
                                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nombre} {$record->apellido_paterno} {$record->apellido_materno}")
                                ->searchable()
                                ->required(),
                        ]),
                        
                        self::getInventarioInfoPlaceholder(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // PASO 2: TIPO DE TALONARIO Y FECHA
                Section::make('Paso 2: Tipo de Talonario y Fecha')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('tipo_talonarios') // CORREGIDO: era 'tipo_talonarioss'
                                ->label('¿Qué tipo de talonario entregará?')
                                ->options(function (callable $get) {
                                    $entregaId = $get('entrega_talonario_id');
                                    $options = [];
                                    
                                    if ($entregaId) {
                                        $entrega = \App\Models\EntregaTalonario::find($entregaId);
                                        if ($entrega) {
                                            if ($entrega->cantidad_restante_preferencial > 0) {
                                                $options['preferencial'] = 'Preferencial';
                                            }
                                            if ($entrega->cantidad_restante_regular > 0) {
                                                $options['regular'] = 'Regular';
                                            }
                                            if ($entrega->cantidad_restante_preferencial > 0 && $entrega->cantidad_restante_regular > 0) {
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
                                ->disabled(fn($get) => !$get('entrega_talonario_id')),

                            Forms\Components\DatePicker::make('fecha_entrega')
                                ->label('Fecha de Entrega')
                                ->default(now()->toDateString())
                                ->disabled()
                                ->required()
                                ->prefixIcon('heroicon-o-calendar'),
                        ]),
                    ])
                    ->columns(1)
                    ->visible(fn($get) => !empty($get('entrega_talonario_id')))
                    ->collapsible(),

                // PASO 3: DETALLES DE PREFERENCIALES
                Forms\Components\Section::make('Paso 3: Detalles de Talonarios Preferenciales')
                    ->visible(fn($get) => in_array($get('tipo_talonarios'), ['preferencial', 'Preferenciales y Regulares']) && $get('entrega_talonario_id'))
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                           Forms\Components\TextInput::make('preferencial_Del')
    ->label('Del')
    ->prefixIcon('heroicon-o-arrow-down')
    ->numeric()
    ->required()
    ->minValue(0),

Forms\Components\TextInput::make('preferencial_Al')
    ->label('Al N°')
    ->prefixIcon('heroicon-o-arrow-up')
    ->numeric()
    ->required()
    ->minValue(1)
    ->gt('preferencial_Del'),

                                
                            Forms\Components\TextInput::make('rango_inicial_preferencial')
                                ->label('Rango Inicial Facturas')
                                ->prefixIcon('heroicon-o-document')
                                ->numeric()
                                ->required()
                                ->default(function () {
                                    $ultimo = \App\Models\EntregaTalonariosAnfitrion::orderByDesc('rango_final_preferencial')->first();
                                    return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                                })
                                ->reactive(),
                        ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // PASO 4: DETALLES DE REGULARES
                Forms\Components\Section::make('Paso 4: Detalles de Talonarios Regulares')
                    ->visible(fn($get) => in_array($get('tipo_talonarios'), ['regular', 'Preferenciales y Regulares']) && $get('entrega_talonario_id'))
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                           TextInput::make('regular_Del')
    ->label('Del')
    ->prefixIcon('heroicon-o-arrow-down')
    ->numeric()
    ->required()
    ->minValue(0),

TextInput::make('regular_Al')
    ->label('Al N°')
    ->prefixIcon('heroicon-o-arrow-up')
    ->numeric()
    ->required()
    ->minValue(1)
    ->rule(fn ($get) => 'gt:' . $get('regular_Del'))
    ->helperText('Debe ser mayor que "Del"'),



                            Forms\Components\TextInput::make('rango_inicial_regular')
                                ->label('Rango Inicial Facturas')
                                ->prefixIcon('heroicon-o-document')
                                ->numeric()
                                ->required()
                                ->default(function () {
                                    $ultimo = \App\Models\EntregaTalonariosAnfitrion::orderByDesc('rango_final_regular')->first();
                                    return $ultimo ? $ultimo->rango_final_regular + 1 : 1;
                                })
                                ->reactive(),
                        ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // PASO 5: INFORMACIÓN ADICIONAL
                Forms\Components\Section::make('Paso 5: Información Adicional y Finalización')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones de Entrega')
                            ->rows(3)
                            ->maxLength(255)
                            ->placeholder('Ingrese observaciones sobre la entrega...'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // COLUMNA PRINCIPAL - INFORMACIÓN BÁSICA
                Tables\Columns\TextColumn::make('detalles')
                    ->label('📋 Detalles')
                    ->getStateUsing(function ($record) {
                        $id = "#ID: {$record->id}";

                        $anfitrion = $record->anfitrion
                            ? "👤 Anfitrión: {$record->anfitrion->nombre} {$record->anfitrion->apellido_paterno}"
                            : "👤 Anfitrión: No disponible";

                        $cajeroEntrega = ($record->entregaTalonario && $record->entregaTalonario->cajero)
                            ? "🏢 Cajero de Patio: {$record->entregaTalonario->cajero->nombre} {$record->entregaTalonario->cajero->apellido_paterno}"
                            : "🏢 Cajero de Patio: Entrega#{$record->entrega_talonario_id} - Sin asignar";

                        return $id . "\n" . $anfitrion . "\n" . $cajeroEntrega;
                    })
                    ->sortable(false)
                    ->searchable(false)
                    ->formatStateUsing(fn ($state) => nl2br(e($state)))
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tipo_talonarios')
                    ->label('📋 Tipo Talonario')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'preferencial' => 'success',
                        'regular' => 'info',
                        'Preferenciales y Regulares' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state === 'Preferenciales y Regulares' ? 'Ambos' : ucfirst($state))
                    ->toggleable(isToggledHiddenByDefault: true),

                // INFORMACIÓN PREFERENCIALES
                Tables\Columns\TextColumn::make('preferenciales_info')
                    ->label('🎫 Preferenciales')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if (!$record->preferencial_Del || !$record->preferencial_Al) { // CORREGIDO
                            return '<span style="color: gray;">No aplica</span>';
                        }

                        $cantidad = ($record->preferencial_Al - $record->preferencial_Del) + 1; // CORREGIDO
                        $rangoTickets = $record->preferencial_Del && $record->preferencial_Al  // CORREGIDO
                            ? "{$record->preferencial_Del} - {$record->preferencial_Al}"  // CORREGIDO
                            : 'No definido';

                        $rangoFacturas = $record->rango_inicial_preferencial && $record->rango_final_preferencial
                            ? "{$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}"
                            : 'No definido';

                        $totalBoletos = $record->total_boletos_preferenciales ?? 0;
                        $cantidadRestante = $record->cantidad_restante_preferencial ?? 0;
                        $totalAproximado = $record->total_aproximado_bolivianos_preferencial ?? 0;

                        // Color para Restantes
                        $colorRestantes = 'green';
                        if ($cantidadRestante <= 50) {
                            $colorRestantes = 'red';
                        } elseif ($cantidadRestante <= 100) {
                            $colorRestantes = 'orange';
                        } elseif ($cantidadRestante < $cantidad) {
                            $colorRestantes = 'yellow';
                        }

                        return "
                        <strong>Cantidad:</strong> {$cantidad} talonarios<br>
                        <strong>Restantes:</strong> <span style='color: {$colorRestantes}; font-weight: bold;'>{$cantidadRestante}</span><br>
                        <strong>Del a Al:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoTickets}</span><br>
                        <strong>Rango Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoFacturas}</span><br>
                        <strong>Total Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>" . (int)$totalBoletos . "</span><br>
                        <strong>Valor Bs:</strong> <span style='color: #32CD32; font-weight: bold;'>Bs. " . number_format($totalAproximado, 2) . "</span>";
                    }),

                // PROGRESO PREFERENCIALES
                CircleProgress::make('preferenciales_circle')
                    ->label('🏷️% Pref.')
                    ->getStateUsing(fn($record) => [
                        'total' => 100,
                        'progress' => $record->cantidad_preferenciales > 0
                            ? round((($record->cantidad_restante_preferencial ?? 0) / $record->cantidad_preferenciales) * 100)
                            : 0,
                    ]),

                ProgressBar::make('preferenciales_progress_bar')
                    ->label('🏷️% Restante Pref.')
                    ->getStateUsing(fn($record) => [
                        'total' => 100,
                        'progress' => $record->cantidad_preferenciales > 0
                            ? round((($record->cantidad_restante_preferencial ?? 0) / $record->cantidad_preferenciales) * 100)
                            : 0,
                    ]),

                // INFORMACIÓN REGULARES
                Tables\Columns\TextColumn::make('regulares_info')
                    ->label('🎟️ Regulares')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if (!$record->regular_Del || !$record->regular_Al) { // CORREGIDO
                            return '<span style="color: gray;">No aplica</span>';
                        }

                        $cantidad = ($record->regular_Al - $record->regular_Del) + 1; // CORREGIDO
                        $rangoTickets = $record->regular_Del && $record->regular_Al  // CORREGIDO
                            ? "{$record->regular_Del} - {$record->regular_Al}"  // CORREGIDO
                            : 'No definido';

                        $rangoFacturas = $record->rango_inicial_regular && $record->rango_final_regular
                            ? "{$record->rango_inicial_regular} - {$record->rango_final_regular}"
                            : 'No definido';

                        $totalBoletos = $record->total_boletos_regulares ?? 0;
                        $cantidadRestante = $record->cantidad_restante_regular ?? 0;
                        $totalAproximado = $record->total_aproximado_bolivianos_regular ?? 0;

                        // Color para Restantes
                        $colorRestantes = 'green';
                        if ($cantidadRestante <= 50) {
                            $colorRestantes = 'red';
                        } elseif ($cantidadRestante <= 100) {
                            $colorRestantes = 'orange';
                        } elseif ($cantidadRestante < $cantidad) {
                            $colorRestantes = 'yellow';
                        }

                        return "
                        <strong>Cantidad:</strong> {$cantidad} talonarios<br>
                        <strong>Restantes:</strong> <span style='color: {$colorRestantes}; font-weight: bold;'>{$cantidadRestante}</span><br>
                        <strong>Del a Al:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoTickets}</span><br>
                        <strong>Rango Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoFacturas}</span><br>
                        <strong>Total Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>" . (int)$totalBoletos . "</span><br>
                        <strong>Valor Bs:</strong> <span style='color: #32CD32; font-weight: bold;'>Bs. " . number_format($totalAproximado, 2) . "</span>";
                    }),

                // PROGRESO REGULARES
                CircleProgress::make('regulares_circle')
                    ->label('🏷️% Reg.')
                    ->getStateUsing(fn($record) => [
                        'total' => 100,
                        'progress' => $record->cantidad_regulares > 0
                            ? round((($record->cantidad_restante_regular ?? 0) / $record->cantidad_regulares) * 100)
                            : 0,
                    ]),

                ProgressBar::make('regulares_progress_bar')
                    ->label('🏷️% Restante Reg.')
                    ->getStateUsing(fn($record) => [
                        'total' => 100,
                        'progress' => $record->cantidad_regulares > 0
                            ? round((($record->cantidad_restante_regular ?? 0) / $record->cantidad_regulares) * 100)
                            : 0,
                    ]),

                // INFORMACIÓN FINANCIERA
                Tables\Columns\TextColumn::make('total_recaudacion_bolivianos')
                    ->label('💰 Total Bs.')
                    ->money('BOB')
                    ->color('success')
                    ->sortable()
                    ->alignEnd()
                    ->default(0)
                    ->description('Total en Bs.')
                    ->toggleable(isToggledHiddenByDefault: true),

                // FECHAS
                Tables\Columns\TextColumn::make('fechas')
                    ->label('🗓️ Fechas')
                    ->getStateUsing(function ($record) {
                        $fechaEntrega = $record->fecha_entrega 
                            ? "📅 Entrega: " . \Carbon\Carbon::parse($record->fecha_entrega)->format('d/m/Y') 
                            : "📅 Entrega: -";

                        $creado = $record->created_at 
                            ? "🕒 Creado: " . $record->created_at->format('d/m/Y H:i') 
                            : "🕒 Creado: -";

                        return $fechaEntrega . "\n" . $creado;
                    })
                    ->sortable(false)
                    ->searchable(false)
                    ->formatStateUsing(fn ($state) => nl2br(e($state)))
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_talonarios')
                    ->options([
                        'preferencial' => 'Preferencial',
                        'regular' => 'Regular',
                        'Preferenciales y Regulares' => 'Ambos'
                    ]),

                Tables\Filters\SelectFilter::make('anfitrion_id')
                    ->label('Anfitrión')
                    ->relationship('anfitrion', 'nombre')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('entrega_talonario_id')
                    ->label('Entrega Cajero')
                    ->relationship('entregaTalonario', 'id')
                    ->searchable(),

                Tables\Filters\Filter::make('fecha_entrega')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Desde'),
                        Forms\Components\DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($query) => $query->whereDate('fecha_entrega', '>=', $data['from']))
                            ->when($data['until'], fn($query) => $query->whereDate('fecha_entrega', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                    ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                    ->color('dager'),
                ])
                 ->label('Acciones')
                 ->icon('heroicon-o-cog')  // Icono de configuración
                 ->button()  // Esto hace que se vea como botón en lugar de 3 puntos
                 ->color('success'),
            ])
           
            ->headerActions([
                CreateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // Método auxiliar para información del inventario
    protected static function getInventarioInfoPlaceholder()
    {
        return Placeholder::make('info_inventario')
            ->label('')
            ->content(function ($get) {
                $entregaId = $get('entrega_talonario_id');
                if (!$entregaId) {
                    return new HtmlString('
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
                            <p class="text-blue-700 dark:text-blue-300 font-medium">Seleccione una entrega de cajero para ver la información disponible</p>
                        </div>
                    ');
                }
                
                $entrega = \App\Models\EntregaTalonario::find($entregaId);
                if (!$entrega) {
                    return new HtmlString('
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-center">
                            <p class="text-red-600 dark:text-red-400 font-medium">Entrega no encontrada</p>
                        </div>
                    ');
                }
                
                return new HtmlString(self::generateEntregaInfo($entrega));
            });
    }

    protected static function generateEntregaInfo($entrega)
    {
        // Calcular datos para preferenciales
        $prefRestante = $entrega->cantidad_restante_preferencial ?? 0;
        $prefTotal = $entrega->cantidad_preferenciales ?? 0;
        $prefPorcentaje = $prefTotal > 0 ? round(($prefRestante / $prefTotal) * 100) : 0;
        $prefEstado = self::calculateStockStatus($prefRestante, $prefTotal);
        
        // Calcular datos para regulares
        $regRestante = $entrega->cantidad_restante_regular ?? 0;
        $regTotal = $entrega->cantidad_regulares ?? 0;
        $regPorcentaje = $regTotal > 0 ? round(($regRestante / $regTotal) * 100) : 0;
        $regEstado = self::calculateStockStatus($regRestante, $regTotal);
        
        // Colores según estado
        $prefColor = self::getColorByStockStatus($prefEstado);
        $regColor = self::getColorByStockStatus($regEstado);
        
        $cajeroNombre = $entrega->cajero ? 
            "{$entrega->cajero->nombre} {$entrega->cajero->apellido_paterno}" : 
            'Sin asignar';
        
        $html = '
        <div class="space-y-3">
            <!-- Información del Cajero -->
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Cajero Origen: ' . $cajeroNombre . '</p>
            </div>

            <!-- Tarjetas compactas para talonarios -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Preferenciales -->
                <div class="bg-white dark:bg-gray-800 border border-purple-100 dark:border-purple-900 rounded-lg p-3 shadow-sm dark:shadow-gray-900">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-semibold text-purple-800 dark:text-purple-300 text-sm">PREFERENCIALES</h4>
                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded text-xs font-bold">' . $prefRestante . ' disp.</span>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Disponibles:</span>
                            <span class="font-bold text-gray-900 dark:text-white">' . $prefRestante . ' / ' . $prefTotal . '</span>
                        </div>
                        
                        <!-- Barra de progreso compacta -->
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="h-full ' . $prefColor['bg'] . '" style="width: ' . $prefPorcentaje . '%"></div>
                        </div>
                        
                        <!-- Estado compacto -->
                        <div class="flex items-center gap-1 ' . $prefColor['text'] . ' text-xs">
                            ' . self::getStockStatusIcon($prefEstado) . '
                            <span class="font-medium">' . self::getCompactStatusText($prefEstado) . '</span>
                        </div>
                    </div>
                </div>
                
                <!-- Regulares -->
                <div class="bg-white dark:bg-gray-800 border border-blue-100 dark:border-blue-900 rounded-lg p-3 shadow-sm dark:shadow-gray-900">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-300 text-sm">REGULARES</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded text-xs font-bold">' . $regRestante . ' disp.</span>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Disponibles:</span>
                            <span class="font-bold text-gray-900 dark:text-white">' . $regRestante . ' / ' . $regTotal . '</span>
                        </div>
                        
                        <!-- Barra de progreso compacta -->
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="h-full ' . $regColor['bg'] . '" style="width: ' . $regPorcentaje . '%"></div>
                        </div>
                        
                        <!-- Estado compacto -->
                        <div class="flex items-center gap-1 ' . $regColor['text'] . ' text-xs">
                            ' . self::getStockStatusIcon($regEstado) . '
                            <span class="font-medium">' . self::getCompactStatusText($regEstado) . '</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumen general -->
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Total disponible: <span class="font-bold text-gray-900 dark:text-white">' . ($prefRestante + $regRestante) . '</span> talonarios 
                    de <span class="font-bold text-gray-900 dark:text-white">' . ($prefTotal + $regTotal) . '</span> en inventario
                </p>
            </div>
        </div>
        ';
        
        return $html;
    }

    protected static function calculateStockStatus($restante, $total)
    {
        if ($restante == 0) return 'agotado';
        if ($restante <= $total * 0.2) return 'bajo';
        if ($restante <= $total * 0.5) return 'medio';
        return 'disponible';
    }

    protected static function getColorByStockStatus($estado)
    {
        switch ($estado) {
            case 'agotado':
                return [
                    'bg' => 'bg-red-500 dark:bg-red-600', 
                    'text' => 'text-red-700 dark:text-red-400'
                ];
            case 'bajo':
                return [
                    'bg' => 'bg-orange-500 dark:bg-orange-600', 
                    'text' => 'text-orange-700 dark:text-orange-400'
                ];
            case 'medio':
                return [
                    'bg' => 'bg-yellow-500 dark:bg-yellow-600', 
                    'text' => 'text-yellow-700 dark:text-yellow-400'
                ];
            case 'disponible':
                return [
                    'bg' => 'bg-green-500 dark:bg-green-600', 
                    'text' => 'text-green-700 dark:text-green-400'
                ];
            default:
                return [
                    'bg' => 'bg-gray-500 dark:bg-gray-600', 
                    'text' => 'text-gray-700 dark:text-gray-400'
                ];
        }
    }

    protected static function getStockStatusIcon($estado)
    {
        switch ($estado) {
            case 'agotado':
                return '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            case 'bajo':
                return '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
            case 'medio':
                return '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
            case 'disponible':
                return '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            default:
                return '';
        }
    }

    protected static function getCompactStatusText($estado)
    {
        switch ($estado) {
            case 'agotado':
                return 'Agotado - Reabastecer';
            case 'bajo':
                return 'Stock Bajo';
            case 'medio':
                return 'Stock Moderado';
            case 'disponible':
                return 'Stock Suficiente';
            default:
                return 'Estado desconocido';
        }
    }

    

    public static function getRelations(): array
    {
        return [];
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