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
use App\Filament\Resources\EntregaTalonarioResource\Widgets\Cajeros;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;

use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;


class EntregaTalonarioResource extends Resource
{

    protected static ?string $model = EntregaTalonario::class;
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel  = 'Inventario del Cajero';

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
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $del = (int) $state;
                                    $al = (int) $get('preferencial_al');

                                    if ($del && $al && $al >= $del) {
                                        $set('cantidad_preferenciales', $al - $del + 1);
                                    }
                                }),

                            Forms\Components\TextInput::make('preferencial_al')
                                ->label('Al')
                                ->prefixIcon('heroicon-o-arrow-up')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $al = (int) $state;
                                    $del = (int) $get('preferencial_del');

                                    if ($del && $al && $al >= $del) {
                                        $set('cantidad_preferenciales', $al - $del + 1);
                                    } else {
                                        $set('cantidad_preferenciales', null);
                                    }
                                })
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
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $del = (int) $state;
                                    $al = (int) $get('regular_al');

                                    if ($del && $al && $al >= $del) {
                                        $set('cantidad_regulares', $al - $del + 1);
                                    }
                                }),

                            Forms\Components\TextInput::make('regular_al')
                                ->label('Al')
                                ->prefixIcon('heroicon-o-arrow-up')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $al = (int) $state;
                                    $del = (int) $get('regular_del');

                                    if ($del && $al && $al >= $del) {
                                        $set('cantidad_regulares', $al - $del + 1);
                                    } else {
                                        $set('cantidad_regulares', null);
                                    }
                                })
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

            // Preferenciales resumen + progreso
            Tables\Columns\TextColumn::make('resumen_preferenciales')
                ->label('🎫 Preferenciales')
                ->html()
                ->getStateUsing(function ($record) {
                    $total = $record->cantidad_preferenciales ?? 1;
                    $restante = $record->cantidad_restante_preferencial ?? 0;
                    $porcentaje = $total > 0 ? round(($restante / $total) * 100) : 0;

                    return "
        <strong>Cantidad:</strong> {$total}<br>
        <strong>Rango Original:</strong> {$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}<br>
        <strong>Del-Al :</strong> {$record->preferencial_del} - {$record->preferencial_al}<br>
        <strong>Restan:</strong> {$restante} ({$porcentaje}%)<br>
        <strong>Total Bs.:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_preferencial, 2, '.', ',') . "
    ";
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

            // Regulares resumen + progreso
            Tables\Columns\TextColumn::make('resumen_regulares')
                ->label('🎟️ Regulares')
                ->html()
                ->getStateUsing(function ($record) {
                    $total = $record->cantidad_regulares ?? 1;
                    $restante = $record->cantidad_restante_regular ?? 0;
                    $porcentaje = $total > 0 ? round(($restante / $total) * 100) : 0;

                    return "
        <strong>Cantidad:</strong> {$total}<br>
        <strong>Rango Original:</strong> {$record->rango_inicial_regular} - {$record->rango_final_regular}<br>
        <strong>del-Al:</strong> {$record->regular_del} - {$record->regular_al}<br>
        <strong>Restan:</strong> {$restante} ({$porcentaje}%)<br>
        <strong>Total Bs.:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_regular, 2, '.', ',') . "
    ";
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
        <p style="margin: 2px 0; line-height: 1.2;">'.htmlspecialchars($nombreCompleto).'</p>
        <p style="margin: 2px 0; line-height: 1.2;">C.I.: '.htmlspecialchars($ci).'</p>
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
              
        PDF generado el: '.\Carbon\Carbon::now()->format('d/m/Y H:i:s').'
    
        '.($pieBase64 ? '<img src="' . $pieBase64 . '">' : 'Dirección de la empresa | Teléfono | Email').'
    </div>
            </body>
            </html>';

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