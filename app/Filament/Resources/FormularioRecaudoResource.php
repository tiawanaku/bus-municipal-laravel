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
                                    $query->where('ci', $state); // Asegúrate de que 'ci' sea el campo correcto en la relación anfitrion
                                })->latest()->first();

                                if ($asignacion) {
                                    $set('anfitrion_id', $asignacion->id_anfitrion);
                                    $set('conductor_id', $asignacion->id_conductor);
                                    $set('bus_id', $asignacion->id_buses);
                                    $set('N_ficha', $asignacion->n_ficha);
                                } else {
                                    Notification::make()
                                        ->title('Carnet no encontrado')
                                        ->body('No se encontró una asignación para el carnet ingresado.')
                                        ->danger()
                                        ->duration(5000) // Aquí probamos con 5000 milisegundos
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
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('anfitrion')
                    ->label('Anfitrión')
                    ->searchable()
                    ->formatStateUsing(
                        fn($record) =>
                        $record->anfitrion->nombre . ' ' .
                            $record->anfitrion->apellido_paterno . ' ' .
                            $record->anfitrion->apellido_materno
                    ),

                TextColumn::make('conductor')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Conductor')
                    ->searchable()
                    ->formatStateUsing(
                        fn($record) =>
                        $record->conductor->nombre . ' ' .
                            $record->conductor->apellido_paterno . ' ' .
                            $record->conductor->apellido_materno
                    ),

                TextColumn::make('bus.numero_bus')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Número Bus')
                    ->searchable(),


                TextColumn::make('rutas')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rutas'),

                TextColumn::make('horario')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Horario'),

                TextColumn::make('n_ficha')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('N° Ficha'),

                TextColumn::make('cantidad_ventas_regulares')
                    ->label('Cant. Ventas Regulares'),

                TextColumn::make('rango_inicial_regulares')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Inicial Regulares'),

                TextColumn::make('rango_final_regulares')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Final Regulares'),

                TextColumn::make('monto_recaudado_regular')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Monto Recaudado Regular')
                    ->formatStateUsing(fn($state) => 'Bs ' . number_format($state, 2))
                    ->sortable()
                    ->color(
                        fn($state) =>
                        $state < 50 ? 'danger' : ($state >= 50 && $state < 90 ? 'warning' : ($state >= 90 ? 'success' : 'primary'))
                    ),



                TextColumn::make('cantidad_ventas_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Cant. Ventas Preferenciales'),

                TextColumn::make('rango_inicial_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Inicial Preferencial'),

                TextColumn::make('rango_final_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rango Final Preferencial'),


                TextColumn::make('monto_recaudado_preferencial')
                    ->label('Monto Recaudado Preferencial')
                    ->formatStateUsing(fn($state) => 'Bs ' . number_format($state, 2))
                    ->sortable()
                    ->color(
                        fn($state) =>
                        $state < 50 ? 'danger' : ($state >= 50 && $state < 90 ? 'warning' : ($state >= 90 ? 'success' : 'primary'))
                    ),




                TextColumn::make('total_recaudo_regular_preferencial')
                    ->label('Total Recaudo')
                    ->formatStateUsing(fn($state) => 'Bs ' . number_format($state, 2))
                    ->colors([
                        'warning' => fn($state) => true, // Siempre amarillo
                    ]),

                TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Fecha de envio')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i'),
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
                        $totalTarjetas = $record->cantidad_ventas_preferenciales + $record->cantidad_ventas_regulares;

                        $html = '
<style>
    body {
        font-family: Arial, sans-serif;
        color: #333;
        font-size: 12px;
    }
    .header {
        text-align: center;
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 20px;
    }
    .header .form-id {
        margin-top: 5px;
        font-weight: bold;
    }
    h1 {
        text-align: center;
        font-size: 22px;
        margin-bottom: 20px;
        color: #004085;
    }
    h3 {
        text-align: center;
        font-size: 16px;
        margin-top: 30px;
        margin-bottom: 10px;
        color: #0056b3;
        border-bottom: 1px solid #ccc;
        padding-bottom: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        text-align: left;
    }
    th {
        background-color: #e9ecef;
        font-weight: bold;
    }
    .total {
        font-size: 14px;
        font-weight: bold;
        color: #155724;
        background-color: #d4edda;
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
</style>

<div class="header">
    <div><strong>SERVICIO DE TRANSPORTE BUS MUNICIPAL</strong></div>
    <div><strong>UNIDAD DE ADMINISTRACIÓN Y RECAUDO</strong></div>
    <div><strong>FORMULARIO DE REGISTRO DE EFECTIVO - VALORES E INSTRUMENTOS</strong></div>
    <div class="form-id">FORM - 001</div>
    <div><strong>FECHA DE REGISTRO:</strong> ' . date("d/m/Y", strtotime($record->created_at)) . '</div>
</div>
<h3>I. DATOS GENERALES</h3>
<table>
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <table>
                <tr><th>ANFITRIÓN</th><td>' . strtoupper($record->anfitrion->nombre . ' ' . $record->anfitrion->apellido_paterno . ' ' . $record->anfitrion->apellido_materno) . '</td></tr>
                <tr><th>CONDUCTOR</th><td>' . strtoupper($record->conductor->nombre . ' ' . $record->conductor->apellido_paterno . ' ' . $record->conductor->apellido_materno) . '</td></tr>
                <tr><th>RUTA</th><td>' . strtoupper($record->rutas) . '</td></tr>
            </table>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <table>
                <tr><th>TURNO</th><td>' . strtoupper($record->horario) . '</td></tr>
                <tr><th>BUS Nº</th><td>' . strtoupper($record->bus->numero_bus) . '</td></tr>
                <tr><th>FICHA Nº</th><td>' . strtoupper($record->N_ficha) . '</td></tr>
            </table>
        </td>
    </tr>
</table>

<h3>RECAUDO PREFERENCIALES</h3>
<table>
    <tr>
        <th>CANTIDAD</th>
        <th>RANGO INICIAL</th>
        <th>MONTO (BS)</th>
    </tr>
    <tr>
        <td>' . strtoupper($record->cantidad_ventas_preferenciales) . '</td>
        <td>' . strtoupper($record->rango_inicial_preferencial) . '</td>
        <td>' . number_format($record->monto_recaudado_preferencial, 2) . '</td>
    </tr>
</table>

<h3>RECAUDO REGULARES</h3>
<table>
    <tr>
        <th>CANTIDAD</th>
        <th>RANGO INICIAL</th>
        <th>MONTO (BS)</th>
    </tr>
    <tr>
        <td>' . strtoupper($record->cantidad_ventas_regulares) . '</td>
        <td>' . strtoupper($record->rango_inicial_regulares) . '</td>
        <td>' . number_format($record->monto_recaudado_regular, 2) . '</td>
    </tr>
</table>

<h3>TOTAL RECAUDADO</h3>
<table>
    <tr>
        <!-- TOTAL (BS) -->
        <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
                <tr>
                    <th class="total">TOTAL (BS)</th>
                </tr>
                <tr>
                    <td class="total">' . number_format($record->total_recaudo_regular_preferencial, 2) . '</td>
                </tr>
            </table>
        </td>

        <!-- GIROS REALIZADOS POR MOLINETE -->
        <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
                <tr>
                    <th class="total">GIROS REALIZADOS POR MOLINETE</th>
                </tr>
                <tr>
                    <td class="total">' . ($record->cantidad_ventas_preferenciales + $record->cantidad_ventas_regulares) . ' GIROS</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- Espacios para firmas: Conductor, Anfitrión, Cajero -->
<table style="width: 100%; margin-top: 40px; text-align: center; border-collapse: collapse;" border="0">
    <tr>
        <td style="width: 33.33%; padding: 0 10px; border: none;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div style="border-top: 1px solid #000; width: 80%; margin-top: 60px;"></div>
                <p style="margin-top: 8px;"><strong>FIRMA Y SELLO CONDUCTOR<br>OBSERVADOR</strong></p>
            </div>
        </td>
        <td style="width: 33.33%; padding: 0 10px; border: none;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div style="border-top: 1px solid #000; width: 80%; margin-top: 60px;"></div>
                <p style="margin-top: 8px;"><strong>FIRMA Y SELLO ANFITRIÓN<br>ENTREGUÉ</strong></p>
            </div>
        </td>
        <td style="width: 33.33%; padding: 0 10px; border: none;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div style="border-top: 1px solid #000; width: 80%; margin-top: 60px;"></div>
                <p style="margin-top: 8px;"><strong>FIRMA Y SELLO CAJERO<br>RECIBÍ CONFORME</strong></p>
            </div>
        </td>
    </tr>
</table>


';

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

                        return response()->streamDownload(
                            fn() => print($pdf->stream()),
                            'formulario_recaudo_' . $record->id . '.pdf'
                        );
                    }),
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