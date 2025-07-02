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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Models\Ruta;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\Filter;

use Filament\Tables\Filters\SelectFilter as TablesSelectFilter;
use Filament\Forms\Components\DatePicker;
use App\Models\User;


class FormularioRecaudoResource extends Resource
{
    protected static ?string $model = FormularioRecaudo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Formulario Recaudo';
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $modelLabel = 'Formulario de Recaudo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sección Datos Generales
                Forms\Components\Section::make('Datos Generales')
                    ->schema([

                        Forms\Components\TextInput::make('buscar_carnet')
    ->label('Buscar Carnet de Anfitrión')
    ->placeholder('Ej: 12345678')
    ->reactive()
    ->debounce(500)
    ->afterStateUpdated(function ($state, callable $set) {
        $asignacion = \App\Models\AsignacionDeBus::whereHas('anfitrion', function ($query) use ($state) {
            $query->where('ci', $state); // Campo CI del anfitrión
        })->latest()->first();

        // Verificar si hay asignación y si está activa
        if ($asignacion) {
            $fechaFin = $asignacion->fin_asignacion ? \Carbon\Carbon::parse($asignacion->fin_asignacion) : null;
            $hoy = \Carbon\Carbon::today();

            $asignacionActiva = is_null($fechaFin) || $fechaFin->greaterThanOrEqualTo($hoy);

            if ($asignacionActiva) {
                $set('anfitrion_id', $asignacion->id_anfitrion);
                $set('conductor_id', $asignacion->id_conductor);
                $set('bus_id', $asignacion->id_buses);
                $set('N_ficha', $asignacion->n_ficha);
            } else {
                \Filament\Notifications\Notification::make()
                    ->title('Asignación caducada')
                    ->body('La asignación encontrada está caducada.')
                    ->danger()
                    ->duration(5000)
                    ->send();
            }
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Carnet no encontrado')
                ->body('No se encontró una asignación para el carnet ingresado.')
                ->danger()
                ->duration(5000)
                ->send();
        }
    }),


                        Forms\Components\Grid::make(6)
                            ->schema([
                                Forms\Components\Select::make('anfitrion_id')
                                    ->label('Anfitrión')
                                    ->options(function () {
                                        return \App\Models\Anfitrion::all()->mapWithKeys(function ($item) {
                                            return [$item->id => $item->nombre . ' ' . $item->apellido_paterno . ' ' . $item->apellido_materno];
                                        })->toArray();
                                    })
                                    ->required(),

                                Forms\Components\Select::make('conductor_id')
                                    ->label('Conductor')
                                    ->options(
                                        \App\Models\Conductor::all()->mapWithKeys(function ($conductor) {
                                            return [
                                                $conductor->id => $conductor->nombre . ' ' . $conductor->apellido_paterno . ' ' . $conductor->apellido_materno,
                                            ];
                                        })->toArray()
                                    )
                                    ->required(),


                                Forms\Components\TextInput::make('N_ficha')
                                    ->label('Nº de Ficha')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\Select::make('bus_id')
                                    ->label('Nº de Bus')
                                    ->options(function () {
                                        return \App\Models\Bus::all()->mapWithKeys(function ($item) {
                                            return [$item->id => $item->numero_bus];
                                        })->toArray();
                                    })
                                    ->required(),

                                Forms\Components\Select::make('rutas')
                                    ->label('Ruta')
                                    ->options([
                                        'norte' => 'Ruta Norte',
                                        'sur' => 'Ruta Sur',
                                    ])
                                    ->required(),

                                Forms\Components\Select::make('horario')
                                    ->label('Turno')
                                    ->options([
                                        'mañana' => 'Mañana',
                                        'tarde' => 'Tarde',
                                    ])
                                    ->required(),


                            ]),
                    ]),

                // Sección Preferencial
                Forms\Components\Section::make('Preferencial')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('cantidad_ventas_preferenciales')
                                    ->label('Tickets Vendidos Preferenciales')
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->numeric(),

                                Forms\Components\TextInput::make('rango_inicial_preferencial')
                                    ->label('Rango Inicial')
                                    ->prefixIcon('heroicon-o-arrow-down')
                                    ->numeric(),
                            ]),
                    ]),

                // Sección Regular
                Forms\Components\Section::make('Regular')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('cantidad_ventas_regulares')
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->label('Tickets vendidos')
                                    ->numeric(),

                                Forms\Components\TextInput::make('rango_inicial_regulares')
                                    ->label('Rango Inicial')
                                    ->prefixIcon('heroicon-o-arrow-down')
                                    ->numeric(),

                            ]),
                    ]),

                // Sección de Confirmación
                Forms\Components\Section::make('Confirmación')
                    ->schema([
                        Forms\Components\Checkbox::make('confirmacion_datos')
                            ->label('Confirmo que los datos ingresados son correctos')
                            ->required()
                            ->accepted()
                            ->inline(false)
                            ->validationMessages([
                                'accepted' => '¿Está segura/o que estos datos son correctos? Debe marcar la casilla para continuar.',
                            ]),

                    ])
                    ->collapsible(),

            ]);
    }



    public static function table(Table $table): Table
    {
       return $table
        ->modifyQueryUsing(function (Builder $query) {
            $user = auth()->user();
            
            // Si es super_admin, puede ver todos los registros
            if ($user->hasRole('super_admin')) {
                return $query; // No aplica ningún filtro
            }
            
            // Para cualquier otro usuario, solo ve sus propios registros
            return $query->where('anfitrion_id', $user->id);
        })
            ->columns([
                // 👥 Información de Personal
                Tables\Columns\TextColumn::make('personal_info')
                ->label('👥 Personal')
                ->html()
                ->getStateUsing(function ($record) {
                    $anfitrion = $record->anfitrion;
                    $conductor = $record->conductor;
                    return "
                        <strong>Anfitrión:</strong> {$anfitrion->nombre} {$anfitrion->apellido_paterno} {$anfitrion->apellido_materno}<br>
                        <strong>Conductor:</strong> {$conductor->nombre} {$conductor->apellido_paterno} {$conductor->apellido_materno}<br>
                        <strong>Bus:</strong> {$record->bus->numero_bus}
                    ";
                }),

            // 📋 Información de Asignación
            Tables\Columns\TextColumn::make('asignacion_info')
                ->label('📋 Asignación')
                ->html()
                ->getStateUsing(function ($record) {
                    return "
                        <strong>N° Ficha:</strong> {$record->N_ficha}<br>
                        <strong>Rutas:</strong> {$record->rutas}<br>
                        <strong>Horario:</strong> {$record->horario}
                    ";
                }),

 // 🎫 Recaudo Regular
Tables\Columns\TextColumn::make('regular_info')
    ->label('🎟️ Regulares')
    ->html()
    ->getStateUsing(function ($record) {
        $ventas = $record->cantidad_ventas_regulares ?? 0;
        $montoRaw = $record->monto_recaudado_regular ?? 0;
        $monto = number_format($montoRaw, 2, '.', ',');

        // Lógica de colores de 3 estados (sin gris)
        if ($montoRaw <= 30) {
            $color = '#dc2626'; // rojo - bajo recaudo
        } elseif ($montoRaw <= 70) {
            $color = '#eab308'; // amarillo - recaudo regular
        } else {
            $color = '#16a34a'; // verde - alto recaudo
        }

        $completos = intdiv($ventas, 50);
        $restantes = $ventas % 50;

        $textoTalonarios = $completos > 0 ? "{$completos} completo" : "";
        if ($restantes > 0) {
            $textoTalonarios .= ($textoTalonarios ? " + " : "") . "1 en uso";
        }

        return "
            <strong>Ventas de Tickets:</strong> {$ventas}<br>
            <strong>Talonarios:</strong> {$textoTalonarios}<br>
            <strong>Rango:</strong> {$record->rango_inicial_regulares} - {$record->rango_final_regulares}<br>
            <strong>Recaudo:</strong> <span style='color: {$color}; font-weight: bold;'>Bs {$monto}</span>
        ";
    }),

// 🎫 Recaudo Preferencial 
Tables\Columns\TextColumn::make('preferencial_info')
    ->label('🎫 Preferenciales')
    ->html()
    ->getStateUsing(function ($record) {
        $ventas = $record->cantidad_ventas_preferenciales ?? 0;
        $montoRaw = $record->monto_recaudado_preferencial ?? 0;
        $monto = number_format($montoRaw, 2, '.', ',');

        // Lógica de colores de 3 estados (sin gris)
        if ($montoRaw <= 30) {
            $color = '#dc2626'; // rojo - bajo recaudo
        } elseif ($montoRaw <= 70) {
            $color = '#eab308'; // amarillo - recaudo regular
        } else {
            $color = '#16a34a'; // verde - alto recaudo
        }

        $completos = intdiv($ventas, 50);
        $restantes = $ventas % 50;

        $textoTalonarios = $completos > 0 ? "{$completos} completo" : "";
        if ($restantes > 0) {
            $textoTalonarios .= ($textoTalonarios ? " + " : "") . "1 en uso";
        }

        return "
            <strong>Ventas de Tickets:</strong> {$ventas}<br>
            <strong>Talonarios:</strong> {$textoTalonarios}<br>
            <strong>Rango:</strong> {$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}<br>
            <strong>Recaudo:</strong> <span style='color: {$color}; font-weight: bold;'>Bs {$monto}</span>
        ";
    }),

           // 💰 Total Recaudo
Tables\Columns\TextColumn::make('total_recaudo')
    ->label('💰 Total')
    ->html()
    ->getStateUsing(function ($record) {
        $totalRaw = $record->total_recaudo_regular_preferencial ?? 0;
        $total = number_format($totalRaw, 2, '.', ',');
        
        // Lógica de colores de 3 estados (sin gris)
        if ($totalRaw <= 60) {
            $color = '#dc2626'; // rojo - bajo recaudo total
        } elseif ($totalRaw <= 140) {
            $color = '#eab308'; // amarillo - recaudo total regular
        } else {
            $color = '#16a34a'; // verde - alto recaudo total
        }
        
        return "<span style='font-size: 18px; color: {$color}; font-weight: bold;'>Bs {$total}</span>";
    }),

            // 🕒 Fechas
            Tables\Columns\TextColumn::make('fechas')
                ->label('🕒 Fechas')
                ->html()
                ->getStateUsing(function ($record) {
                    return "
                        <strong>Enviado:</strong> " . date('d/m/Y H:i', strtotime($record->created_at)) . "<br>
                    ";
                })
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultSort('id', 'desc')

            ->filters([
                SelectFilter::make('anfitrion_id')
                    ->label('Anfitrión')
                    ->relationship('anfitrion', 'nombre', fn($query) => $query->orderBy('nombre'))
                    ->searchable(),

                Filter::make('created_exact')
                    ->form([
                        DatePicker::make('date')->label('Día exacto'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['date'],
                            fn($q) => $q->whereDate('created_at', $data['date']) // 👈 solo la fecha exacta
                        );
                    })
                    ->label('Fecha de envío exacta')
                    ->indicateUsing(function (array $data): ?string {
                        return $data['date']
                            ? 'Fecha seleccionada: ' . \Carbon\Carbon::parse($data['date'])->format('d/m/Y')
                            : null;
                    }),

            ])

            ->actions([
                Tables\Actions\EditAction::make(),

     Tables\Actions\Action::make('descargar_pdf')
    ->label('Descargar PDF')
    ->icon('heroicon-o-document-arrow-down')
    ->color('success')
    ->action(function ($record) {
        $totalGiros = \DB::table('control_de_reguladors')
            ->where('bus_id', $record->bus_id)
            ->latest('created_at')
            ->value('total_giros') ?? 0;

        $girosRealizadosAnfitrion = $record->cantidad_ventas_preferenciales + $record->cantidad_ventas_regulares;
        $diferencia = $totalGiros - $girosRealizadosAnfitrion;

        $datosRegulador = \DB::table('control_de_reguladors')
            ->where('bus_id', $record->bus_id)
            ->latest('created_at')
            ->first();

        if ($diferencia == 0) {
            $variacionTexto = "✓ COINCIDENCIA EXACTA - Sin diferencias";
            $variacionColor = "#28a745";
            $estadoCruce = "CONFORME";
        } elseif ($diferencia > 0) {
            $variacionTexto = "⚠️ DIFERENCIA - Regulador reporta $diferencia giros más";
            $variacionColor = "#ffc107";
            $estadoCruce = "REVISAR";
        } else {
            $variacionTexto = "❌ DIFERENCIA - Anfitrión reporta " . abs($diferencia) . " giros más";
            $variacionColor = "#dc3545";
            $estadoCruce = "REVISAR";
        }

        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulario de Registro de Recaudo</title>
    <style>
        @page { 
            margin: 15mm 10mm; 
            size: A4; 
        }
        body { 
            font-family: "Times New Roman", serif; 
            font-size: 10px; 
            line-height: 1.3; 
            margin: 0; 
            padding: 0; 
            color: #000;
            background: #fff;
        }
        .document-container { 
            max-width: 100%; 
            margin: 0 auto; 
            padding: 0; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border: 2px solid #000; 
            padding: 10px; 
            background: #fff;
        }
        .header .title { 
            font-size: 14px; 
            font-weight: bold; 
            margin-bottom: 5px;
            text-transform: uppercase;
            color: #000;
        }
        .header .subtitle { 
            font-size: 11px; 
            margin-bottom: 3px; 
            font-weight: bold;
            color: #000;
        }
        .header .form-info { 
            font-size: 10px; 
            margin-top: 8px; 
            padding-top: 5px;
            border-top: 1px solid #000;
            color: #000;
        }
        .section-title { 
            background: #000; 
            color: #fff; 
            font-size: 11px; 
            padding: 6px 10px; 
            margin: 15px 0 8px 0; 
            font-weight: bold; 
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #000;
        }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 9px; 
            margin-bottom: 8px; 
            border: 2px solid #000;
        }
        .data-table th { 
            background: #000; 
            color: #fff;
            padding: 6px 4px; 
            text-align: center; 
            border: 1px solid #000; 
            font-weight: bold;
            font-size: 9px;
        }
        .data-table td { 
            padding: 5px 4px; 
            border: 1px solid #000; 
            text-align: center;
            vertical-align: middle;
            background: #fff;
            color: #000;
        }
        .data-table .label { 
            font-weight: bold; 
            background: #f5f5f5;
            text-align: left;
            padding-left: 6px;
            color: #000;
        }
        .data-table .value { 
            text-align: center; 
            font-weight: bold;
            color: #000;
        }
        .highlight { 
            font-weight: bold; 
            color: #000;
            text-decoration: underline;
        }
        .money { 
            font-weight: bold; 
            color: #000;
        }
        
        /* Contenedor para tablas lado a lado */
        .tables-row { 
            display: table; 
            width: 100%; 
            margin-bottom: 10px;
        }
        .table-cell { 
            display: table-cell; 
            width: 50%; 
            padding-right: 5px;
            vertical-align: top;
        }
        .table-cell:last-child { 
            padding-right: 0; 
            padding-left: 5px;
        }
        
        .variation-section { 
            margin: 15px 0; 
            padding: 8px; 
            background: #fff; 
            color: #000; 
            text-align: center; 
            font-weight: bold; 
            font-size: 11px;
            border: 2px solid #000;
            text-transform: uppercase;
        }
        .signatures { 
            margin-top: 25px; 
            display: table; 
            width: 100%;
        }
        .signature { 
            display: table-cell; 
            text-align: center; 
            width: 33.33%; 
            padding: 0 10px;
            vertical-align: top;
        }
        .signature-line { 
            border-top: 2px solid #000; 
            margin-top: 25px; 
            margin-bottom: 5px;
        }
        .signature-text {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
        }
        .footer { 
            font-size: 8px; 
            text-align: center; 
            margin-top: 20px; 
            border-top: 2px solid #000; 
            padding-top: 8px; 
            color: #000;
        }
        .general-info { 
            border: 2px solid #000; 
            margin-bottom: 10px;
        }
        .general-info td { 
            padding: 8px; 
            border-right: 1px solid #000;
            background: #fff;
        }
        .general-info td:last-child { 
            border-right: none;
        }
        .status-conforme {
            color: #000;
            font-weight: bold;
        }
        .status-revisar {
            color: #000;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="document-container">
        <div class="header">
            <div class="title">Servicio de Transporte Bus Municipal</div>
            <div class="subtitle">Unidad de Administración y Recaudo</div>
            <div class="subtitle">Formulario de Registro de Efectivo - Valores e Instrumentos</div>
            <div class="form-info">
                <strong>FORM - 001</strong> | 
                <strong>FECHA DE REGISTRO:</strong> ' . date("d/m/Y", strtotime($record->created_at)) . ' | 
                <strong>HORA:</strong> ' . date("H:i", strtotime($record->created_at)) . '
            </div>
        </div>

        <div class="section-title">I. Datos Generales del Servicio</div>
        <table class="data-table general-info">
            <tr>
                <td width="25%">
                    <div class="label">ANFITRIÓN:</div>
                    <div class="value">' . $record->anfitrion->nombre . ' ' . $record->anfitrion->apellido_paterno . '</div>
                </td>
                <td width="25%">
                    <div class="label">CONDUCTOR:</div>
                    <div class="value">' . $record->conductor->nombre . ' ' . $record->conductor->apellido_paterno . '</div>
                </td>
                <td width="25%">
                    <div class="label">RUTA:</div>
                    <div class="value">' . $record->rutas . '</div>
                </td>
                <td width="25%">
                    <div class="label">TURNO:</div>
                    <div class="value">' . $record->horario . '</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">BUS Nº:</div>
                    <div class="value highlight">' . $record->bus->numero_bus . '</div>
                </td>
                <td>
                    <div class="label">FICHA Nº:</div>
                    <div class="value">' . $record->N_ficha . '</div>
                </td>
                <td colspan="2">
                    <div class="label">OBSERVACIONES:</div>
                    <div style="height: 15px; border-bottom: 1px dotted #666;"></div>
                </td>
            </tr>
        </table>

        <div class="section-title">II. Recaudo de Pasajes</div>
        <div class="tables-row">
            <div class="table-cell">
                <table class="data-table">
                    <tr><th colspan="3">RECAUDO PREFERENCIALES</th></tr>
                    <tr>
                        <th width="33%">CANTIDAD</th>
                        <th width="34%">RANGO INICIAL</th>
                        <th width="33%">MONTO (Bs)</th>
                    </tr>
                    <tr>
                        <td class="highlight">' . number_format($record->cantidad_ventas_preferenciales) . '</td>
                        <td>' . $record->rango_inicial_preferencial . '</td>
                        <td class="money">' . number_format($record->monto_recaudado_preferencial, 2) . '</td>
                    </tr>
                </table>
            </div>
            <div class="table-cell">
                <table class="data-table">
                    <tr><th colspan="3">RECAUDO REGULARES</th></tr>
                    <tr>
                        <th width="33%">CANTIDAD</th>
                        <th width="34%">RANGO INICIAL</th>
                        <th width="33%">MONTO (Bs)</th>
                    </tr>
                    <tr>
                        <td class="highlight">' . number_format($record->cantidad_ventas_regulares) . '</td>
                        <td>' . $record->rango_inicial_regulares . '</td>
                        <td class="money">' . number_format($record->monto_recaudado_regular, 2) . '</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="section-title">III. Control y Verificación</div>
        <div class="tables-row">
            <div class="table-cell">
                <table class="data-table">
                    <tr><th colspan="2">CONTROL MOLINETE</th></tr>
                    <tr>
                        <th width="50%">MOLINETE INICIAL</th>
                        <th width="50%">MOLINETE FINAL</th>
                    </tr>
                    <tr>
                        <td class="highlight">' . ($datosRegulador->molinete_inicial ?? 'N/A') . '</td>
                        <td class="highlight">' . ($datosRegulador->molinete_final ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>TOTAL GIROS MOLINETE</th>
                        <th>FECHA REGISTRO</th>
                    </tr>
                    <tr>
                        <td class="money">' . number_format($totalGiros) . '</td>
                        <td>' . ($datosRegulador ? date("d/m/Y H:i", strtotime($datosRegulador->created_at)) : 'N/A') . '</td>
                    </tr>
                </table>
            </div>
            <div class="table-cell">
                <table class="data-table">
                    <tr><th colspan="2">COMPARACIÓN DE GIROS</th></tr>
                    <tr>
                        <th width="50%">GIROS ANFITRIÓN</th>
                        <th width="50%">DIFERENCIA</th>
                    </tr>
                    <tr>
                        <td class="highlight">' . number_format($girosRealizadosAnfitrion) . '</td>
                        <td class="' . ($estadoCruce == "CONFORME" ? "status-conforme" : "status-revisar") . '">' . abs($diferencia) . '</td>
                    </tr>
                    <tr>
                        <th>TOTAL RECAUDADO (Bs)</th>
                        <th>ESTADO CRUCE</th>
                    </tr>
                    <tr>
                        <td class="money">' . number_format($record->total_recaudo_regular_preferencial, 2) . '</td>
                        <td class="' . ($estadoCruce == "CONFORME" ? "status-conforme" : "status-revisar") . '">' . $estadoCruce . '</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="variation-section">
            ' . $variacionTexto . '
        </div>

        <div class="section-title">IV. Firmas y Validaciones</div>
        <div class="signatures">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-text">Firma y Sello<br>Conductor Observador</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-text">Firma y Sello<br>Anfitrión - Entregué</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-text">Firma y Sello<br>Cajero - Recibí Conforme</div>
            </div>
        </div>

        <div class="footer">
            <strong>Documento Oficial Generado Automáticamente</strong><br>
            Fecha y Hora: ' . date("d/m/Y H:i:s") . ' | Sistema de Control de Transporte Municipal<br>
            <em>Este documento tiene validez administrativa según normativa vigente</em>
        </div>
    </div>
</body>
</html>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Times-Roman'
        ]);

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            'formulario_recaudo_' . $record->bus->numero_bus . '_' . date('Y-m-d_H-i') . '.pdf'
        );
    })
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