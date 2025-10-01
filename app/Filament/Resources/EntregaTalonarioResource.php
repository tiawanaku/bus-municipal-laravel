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
use Barryvdh\DomPDF\Facade\Pdf;

use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Notifications\Notification;

use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ActionGroup;
use App\Models\Cajero;

use Illuminate\Support\HtmlString;
use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

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
                                    return \App\Models\InventarioTalonarios::where('estado', 'disponible')
                                        ->where('estado', '!=', 'agotado')
                                        ->orderBy('created_at', 'asc')
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
                        
                        self::getInventarioInfoPlaceholder(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // PASO 2: TIPO DE TALONARIO Y FECHA
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
                                ->disabled(fn($get) => !$get('inventario_id')),

                           Forms\Components\DatePicker::make('fecha_entrega')
    ->label('Fecha de Entrega')
    ->default(now()->toDateString()) // Fecha actual
    ->disabled() // El usuario no puede modificarla
    ->required()
    ->prefixIcon('heroicon-o-calendar'),

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
                                ->gt('preferencial_del'),
                                
                            Forms\Components\TextInput::make('rango_inicial_preferencial')
                                ->label('Rango Inicial Facturas')
                                ->prefixIcon('heroicon-o-document')
                                ->numeric()
                                ->required()
                                ->default(function () {
                                    $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_preferencial')->first();
                                    return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                                })
                                ->reactive(),
                        ]),
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
                                ->gt('regular_del'),

                            Forms\Components\TextInput::make('rango_inicial_regular')
                                ->label('Rango Inicial Facturas')
                                ->prefixIcon('heroicon-o-document')
                                ->numeric()
                                ->required()
                                ->default(function () {
                                    $ultimo = \App\Models\EntregaTalonario::orderByDesc('rango_final_regular')->first();
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

        $cajeroReceptor = $record->cajero
            ? "👤 Cajero: {$record->cajero->nombre} {$record->cajero->apellido_paterno}"
            : "👤 Cajero: No disponible";

        $cajeroInventario = ($record->inventario && $record->inventario->cajero)
            ? "🏢 Custodio: {$record->inventario->cajero->nombre} {$record->inventario->cajero->apellido_paterno}"
            : "🏢 Custodio: Inv.#{$record->inventario_id} - Sin asignar";

        return $id . "\n" . $cajeroReceptor . "\n" . $cajeroInventario;
    })
    ->sortable(false)
    ->searchable(false)
    ->formatStateUsing(fn ($state) => nl2br(e($state))) // para que respete saltos de línea en HTML
    ->html() // importante para que renderice el <br>
    ->toggleable(isToggledHiddenByDefault: true),



               Tables\Columns\TextColumn::make('tipo_talonario')
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


                // INFORMACIÓN PREFERENCIALES - MISMOS ESTILOS QUE INVENTARIO
                Tables\Columns\TextColumn::make('preferenciales_info')
                    ->label('🎫 Preferenciales')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if (!$record->preferencial_del || !$record->preferencial_al) {
                            return '<span style="color: gray;">No aplica</span>';
                        }

                        $cantidad = ($record->preferencial_al - $record->preferencial_del) + 1;
                        $rangoTickets = $record->preferencial_del && $record->preferencial_al 
                            ? "{$record->preferencial_del} - {$record->preferencial_al}" 
                            : 'No definido';

                        $rangoFacturas = $record->rango_inicial_preferencial && $record->rango_final_preferencial
                            ? "{$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}"
                            : 'No definido';

                        $totalBoletos = $record->total_boletos_preferenciales ?? 0;
                        $cantidadRestante = $record->cantidad_restante_preferencial ?? 0;
                        $totalAproximado = $record->total_aproximado_bolivianos_preferencial ?? 0;

                        // Color para Restantes (misma lógica que inventario)
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

                // INFORMACIÓN REGULARES - MISMOS ESTILOS QUE INVENTARIO
                Tables\Columns\TextColumn::make('regulares_info')
                    ->label('🎟️ Regulares')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if (!$record->regular_del || !$record->regular_al) {
                            return '<span style="color: gray;">No aplica</span>';
                        }

                        $cantidad = ($record->regular_al - $record->regular_del) + 1;
                        $rangoTickets = $record->regular_del && $record->regular_al 
                            ? "{$record->regular_del} - {$record->regular_al}" 
                            : 'No definido';

                        $rangoFacturas = $record->rango_inicial_regular && $record->rango_final_regular
                            ? "{$record->rango_inicial_regular} - {$record->rango_final_regular}"
                            : 'No definido';

                        $totalBoletos = $record->total_boletos_regulares ?? 0;
                        $cantidadRestante = $record->cantidad_restante_regular ?? 0;
                        $totalAproximado = $record->total_aproximado_bolivianos_regular ?? 0;

                        // Color para Restantes (misma lógica que inventario)
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
    ->formatStateUsing(fn ($state) => nl2br(e($state))) // respetar saltos de línea
    ->html()
    ->toggleable(isToggledHiddenByDefault: true),

               
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'devuelto' => 'Devuelto',
                        'agotado' => 'Agotado'
                    ]),

                Tables\Filters\SelectFilter::make('tipo_talonario')
                    ->options([
                        'preferencial' => 'Preferencial',
                        'regular' => 'Regular',
                        'Preferenciales y Regulares' => 'Ambos'
                    ]),

                Tables\Filters\SelectFilter::make('cajero_id')
                    ->label('Cajero')
                    ->relationship('cajero', 'nombre')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('inventario_id')
                    ->label('Inventario')
                    ->relationship('inventario', 'id')
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
        
    Action::make('generar_pdf')
    ->label('Generar PDF')
    ->icon('heroicon-o-document')
    ->color('success')
    ->action(function ($record) {
        try {
            $cajero = \App\Models\Cajero::find($record->cajero_id);

            // Función para limpiar caracteres UTF-8 inválidos
            $limpiar = function ($texto) {
                if (is_null($texto) || $texto === '') return '';
                $texto = (string) $texto;
                $texto = str_replace("\0", '', $texto);
                $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
                $texto = @iconv('UTF-8', 'UTF-8//IGNORE', $texto);
                $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $texto);
                return trim($texto);
            };

            $nombreCompleto = $cajero 
                ? trim($limpiar($cajero->nombre) . ' ' . $limpiar($cajero->apellido_paterno) . ' ' . $limpiar($cajero->apellido_materno))
                : 'No disponible';
            
            $ci = $cajero ? $limpiar($cajero->ci) : 'No disponible';
            $tipo_talonario = $limpiar($record->tipo_talonario ?? 'No especificado');

            $filasTabla = '';

            // Preferenciales
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

            // Regulares
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

            // Fecha en español
            try {
                $fechaActual = \Carbon\Carbon::now()
                    ->locale('es')
                    ->isoFormat('D [dias del mes de] MMMM [del ano] YYYY');
                $fechaActual = $limpiar($fechaActual);
            } catch (\Exception $e) {
                $fechaActual = date('d/m/Y');
            }

            // Imágenes
            $encabezadoPath = public_path('img/Galeria/encabezado.png');
            $piePath = public_path('img/Galeria/pie de pagina.png');
            
            $encabezadoBase64 = '';
            $pieBase64 = '';
            
            if (file_exists($encabezadoPath)) {
                try {
                    $encabezadoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($encabezadoPath));
                } catch (\Exception $e) {}
            }
            
            if (file_exists($piePath)) {
                try {
                    $pieBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($piePath));
                } catch (\Exception $e) {}
            }

            // HTML del PDF - SIN caracteres especiales problemáticos
            $html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
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
        .pie-pagina {
            margin-top: 80px;
            text-align: center;
            clear: both;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
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
        ($encabezadoBase64 ? '<img src="' . $encabezadoBase64 . '" alt="Encabezado">' : '<h3>EMPRESA - ENCABEZADO</h3>') . '
    </div>

    <h2>ACTA DE ENTREGA DE TALONARIOS</h2>

    <p>Mediante la presente Acta, se efectua la entrega de talonarios <strong>' . mb_strtoupper(htmlspecialchars($tipo_talonario, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) . '</strong> a la siguiente persona:</p>

    <table>
        <tr><th>N.</th><th>CAJERO (A)</th><th>C.I.</th></tr>
        <tr>
            <td>1</td>
            <td>' . htmlspecialchars($nombreCompleto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>
            <td>' . htmlspecialchars($ci, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>
        </tr>
    </table>

    <p>Al respecto, se aclara que los mismos se haran responsables por la asignacion y recaudo de las FACTURAS PRE VALORADAS, siendo el rango de tickets y facturas de acuerdo al siguiente detalle:</p>

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

    <p>El incumplimiento, si corresponde, sera pasivo a sanciones administrativas. Dando el asentimiento al contenido de la presente Acta de Corresponsabilidad, es firmado en la ciudad de El Alto, a los ' . htmlspecialchars($fechaActual, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '.</p>

    <table style="width: 100%; border-collapse: collapse; border: none;">
        <tr>
            <td style="width: 50%; padding: 0; border: none; vertical-align: top;">
                <div style="margin-top: 15px; text-align: center; margin-left: 20px;">
                    <p style="margin: 2px 0; line-height: 1.2;">_________________________</p>
                    <p style="margin: 2px 0; line-height: 1.2;"><strong>Firma del Cajero</strong></p>
                    <p style="margin: 2px 0; line-height: 1.2;">' . htmlspecialchars($nombreCompleto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>
                    <p style="margin: 2px 0; line-height: 1.2;">C.I.: ' . htmlspecialchars($ci, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>
                </div>
            </td>
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
        PDF generado el: ' . \Carbon\Carbon::now()->format('d/m/Y H:i:s') . '<br>
        ' . ($pieBase64 ? '<img src="' . $pieBase64 . '" style="max-width:100%;" alt="Pie">' : 'Direccion de la empresa | Telefono | Email') . '
    </div>
</body>
</html>';

            // Generar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('letter')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'DejaVu Sans',
                    'debugCss' => false,
                    'debugLayout' => false,
                ]);

            // Nombre del archivo
            $safeName = \Illuminate\Support\Str::slug($nombreCompleto, '_');
            if (!$safeName || strlen($safeName) < 3) {
                $safeName = 'cajero_' . ($record->id ?? 'doc');
            }
            $filename = 'acta_entrega_' . $safeName . '_' . now()->format('Ymd_His') . '.pdf';

            // SOLUCIÓN AL ERROR: Usar streamDownload en lugar de download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generando PDF: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            \Filament\Notifications\Notification::make()
                ->title('Error al generar PDF')
                ->body('No se pudo generar el documento. Revise los logs.')
                ->danger()
                ->send();

            return null;
        }
    })
    ->requiresConfirmation()
    ->modalHeading('Generar PDF')
    ->modalDescription('Se generara el acta de entrega de talonarios.')
    ->modalSubmitActionLabel('Generar'),

Tables\Actions\EditAction::make()
->color('warning'),
 Tables\Actions\DeleteAction::make(),
])
->label('Acciones')
    ->icon('heroicon-o-cog')  // Icono de configuración
    ->button()  // Esto hace que se vea como botón en lugar de 3 puntos
    ->color('success'),
            ])

            ->headerActions([
        CreateAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                     Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }



// Método auxiliar para información del inventario (versión mejorada con HTML)
// Método auxiliar para información del inventario (versión compatible con tema oscuro)
protected static function getInventarioInfoPlaceholder()
{
    return Placeholder::make('info_inventario')
        ->label('')
        ->content(function ($get) {
            $inventarioId = $get('inventario_id');
            if (!$inventarioId) {
                return new HtmlString('
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
                        <p class="text-blue-700 dark:text-blue-300 font-medium">Seleccione un inventario para ver la información disponible</p>
                    </div>
                ');
            }
            
            $inventario = \App\Models\InventarioTalonarios::find($inventarioId);
            if (!$inventario) {
                return new HtmlString('
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-center">
                        <p class="text-red-600 dark:text-red-400 font-medium">Inventario no encontrado</p>
                    </div>
                ');
            }
            
            return new HtmlString(self::generateInventarioInfo($inventario));
        });
}

protected static function generateInventarioInfo($inventario)
{
    // Calcular datos para preferenciales
    $prefRestante = $inventario->cantidad_restante_preferencial ?? 0;
    $prefTotal = $inventario->cantidad_preferenciales ?? 0;
    $prefPorcentaje = $prefTotal > 0 ? round(($prefRestante / $prefTotal) * 100) : 0;
    $prefEstado = self::calculateStockStatus($prefRestante, $prefTotal);
    
    // Calcular datos para regulares
    $regRestante = $inventario->cantidad_restante_regular ?? 0;
    $regTotal = $inventario->cantidad_regulares ?? 0;
    $regPorcentaje = $regTotal > 0 ? round(($regRestante / $regTotal) * 100) : 0;
    $regEstado = self::calculateStockStatus($regRestante, $regTotal);
    
    // Colores según estado (compatibles con tema oscuro)
    $prefColor = self::getColorByStockStatus($prefEstado);
    $regColor = self::getColorByStockStatus($regEstado);
    
    $html = '
    <div class="space-y-3">
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

// Los demás métodos actualizados para tema oscuro
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
    // Los SVG se mantienen igual, pero se adaptarán al color del texto
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
// Los métodos getStockStatus, getColorByStatus, getStatusIcon y restaurarInventario 
// se mantienen igual que en tu código original

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