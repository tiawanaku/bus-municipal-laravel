<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioTalonariosResource\Pages;
use App\Models\InventarioTalonarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use App\Models\Cajero;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\CreateAction;

use Filament\Tables\Actions\Action;
use Carbon\Carbon;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\BulkAction;

use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;




class InventarioTalonariosResource extends Resource
{
    protected static ?string $model = InventarioTalonarios::class;

    protected static ?string $navigationLabel = 'Inventarios Principal';
    protected static ?string $navigationGroup = 'Gestión de Talonarios';
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('DATOS DE DOSIFICACION')
                    ->schema([
                        Grid::make(4)->schema([

                            FileUpload::make('cite_nota_solicitud')
                                ->label('CITE Nota de Solicitud')
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(10240)
                                ->storeFileNamesIn('cite_nota_solicitud_filename')
                                ->directory('notas_solicitud')
                                ->required(),

                            TextInput::make('n_cite')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->label('Nº de Cite'),

                            TextInput::make('gestion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Gestión')
                                ->disabled()
                                ->default(date('Y'))
                                ->readOnly(),

                            TextInput::make('n_dosificacion')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->label('Nº de Dosificación'),

                            TextInput::make('numero_autorizacion')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->label('Nº de Autorización'),

                            DatePicker::make('fecha_solicitud_dosificacion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Fecha de solicitud de dosificación'),

                            DatePicker::make('fecha_autorizacion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Fecha de Autorización'),

                            DatePicker::make('fecha_activacion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Fecha de Activación'),

                        ]),
                    ])
                    ->columns(1),

                // Selección del tipo de talonario
                Select::make('tipo_talonarios')
                    ->label('TIPO DE TALONARIO A ASIGNAR')
                    ->prefixIcon('heroicon-o-hashtag')
                    ->options([
                        'preferenciales' => 'Preferenciales',
                        'regulares'      => 'Regulares',
                        'ambos'          => 'Preferenciales y Regulares',
                    ])
                    ->required()
                    ->reactive()
                    ->columnSpanFull()
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

                Grid::make(3)->schema([

                    Select::make('cajero_id')
                        ->label('Custodio')
                        ->prefixIcon('heroicon-o-user')
                        ->options(function () {
                            return Cajero::where('tipo_cajero', 'principal')
                                ->get()
                                ->mapWithKeys(function ($cajero) {
                                    return [$cajero->id => $cajero->nombre_completo ?: 'Nombre no disponible'];
                                });
                        })
                        ->searchable()
                        ->required(),

                    Select::make('estado')
                        ->label('Estado General')
                        ->options([
                            'disponible' => 'Disponible',
                            'agotado' => 'Agotado',
                            'inactivo' => 'Inactivo',
                        ])
                        ->default('disponible'),
               
                   DatePicker::make('fecha_entrega')
                    ->label('Fecha de Entrega')
                    ->prefixIcon('heroicon-o-calendar')
                    ->default(now())
                    ->disabled() // o ->readOnly()
                    ->required(),
                ]),

               // SECCIÓN PREFERENCIALES
Forms\Components\Section::make('TALONARIOS PREFERENCIALES')
    ->icon('heroicon-o-star')
    ->schema([
        Grid::make(3)
            ->schema([
               TextInput::make('preferencial_del')
    ->label('Del')
    ->prefixIcon('heroicon-o-arrow-down')
    ->numeric()
    ->required()
    ->minValue(0),


                TextInput::make('preferencial_al')
                    ->label('Al')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->gt('preferencial_del') // ← Validación simple
                    ->helperText('Debe ser mayor que "Del"'),

                TextInput::make('rango_inicial_preferencial')
                    ->label('Rango Inicial de Tickets')
                    ->prefixIcon('heroicon-o-arrow-right')
                    ->numeric()
                    ->default(function () {
                        $ultimo = InventarioTalonarios::orderByDesc('rango_final_preferencial')->first();
                        return $ultimo ? $ultimo->rango_final_preferencial + 1 : 0;
                    })
                    ->required()
                    ->minValue(1),
            ]),
    ])
    ->visible(fn($get) => $get('show_preferenciales') ?? false)
    ->collapsible(),

// SECCIÓN REGULARES
Forms\Components\Section::make('TALONARIOS REGULARES')
    ->icon('heroicon-o-document')
    ->schema([
        Grid::make(3)
            ->schema([
               TextInput::make('regular_del')
    ->label('Del')
    ->numeric()
    ->required()
    ->minValue(0),


                TextInput::make('regular_al')
                    ->label('Al')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->gt('regular_del') // ← Validación simple
                    ->helperText('Debe ser mayor que "Del"'),

                TextInput::make('rango_inicial_regular')
                    ->label('Rango Inicial de Tickets')
                    ->prefixIcon('heroicon-o-arrow-right')
                    ->numeric()
                    ->default(function () {
                        $ultimo = InventarioTalonarios::orderByDesc('rango_final_regular')->first();
                        return $ultimo ? $ultimo->rango_final_regular + 1 : 0;
                    })
                    ->required()
                    ->minValue(1),
            ]),
    ])
    ->visible(fn($get) => $get('show_regulares') ?? false)
    ->collapsible(),

                // Observaciones
                Forms\Components\Section::make('OBSERVACIONES')
                    ->schema([
                        Textarea::make('observaciones')
                            ->label('Observaciones Generales')
                            ->rows(3),
                    ])
                    ->columns(1),
            ]);
    }





public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ID del inventario
            Tables\Columns\TextColumn::make('id')
                ->label('#️⃣ ID') 
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

           // Cajero Principal
Tables\Columns\TextColumn::make('cajero.nombre') // Cambiar a la columna real
    ->label('👤Cajero Principal')
    ->searchable(['nombre', 'apellido_paterno', 'apellido_materno']) // Buscar en múltiples columnas
    ->sortable()
    ->formatStateUsing(function ($record) {
        if ($record->cajero) {
            return $record->cajero->nombre . ' ' . 
                   $record->cajero->apellido_paterno . ' ' . 
                   ($record->cajero->apellido_materno ?? '');
        }
        return 'No asignado';
    })->toggleable(isToggledHiddenByDefault: true),

           // Información Preferenciales - Mejorada con todos los campos
Tables\Columns\TextColumn::make('preferenciales_info')
    ->label('🎫 Preferenciales')
    ->html()
    ->getStateUsing(function ($record) {
        if (!$record->cantidad_preferenciales || $record->cantidad_preferenciales == 0) {
            return '<span style="color: gray;">No aplica</span>';
        }

        $rangoTickets = $record->preferencial_del && $record->preferencial_al 
            ? "{$record->preferencial_del} - {$record->preferencial_al}" 
            : 'No definido';

        $rangoFacturas = $record->rango_inicial_preferencial && $record->rango_final_preferencial
            ? "{$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}"
            : 'No definido';

        $totalBoletos = $record->total_boletos_preferenciales ?? 0;
        $cantidadRestante = $record->cantidad_restante_preferencial ?? 0;
        $totalAproximado = $record->total_aproximado_bolivianos_preferencial ?? 0;

        // Color para Restantes (verde cuando está completo, cambia según cantidad)
        $colorRestantes = 'green'; // Completo por defecto
        if ($cantidadRestante <= 50) {
            $colorRestantes = 'red'; // Rojo cuando quedan 50 o menos
        } elseif ($cantidadRestante <= 100) {
            $colorRestantes = 'orange'; // Naranja cuando quedan 100 o menos
        } elseif ($cantidadRestante < $record->cantidad_preferenciales) {
            $colorRestantes = 'yellow'; // Amarillo cuando hay algunos usados
        }

        return "
        <strong>Cantidad Total:</strong> {$record->cantidad_preferenciales} talonarios<br>
        <strong>Restantes:</strong> <span style='color: {$colorRestantes}; font-weight: bold;'>{$cantidadRestante}</span><br>
        <strong>Del a Al:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoTickets}</span><br>
        <strong>Rango Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoFacturas}</span><br>
        <strong>Total Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>" . (int)$totalBoletos . "</span><br>
        <strong>Valor en Bs:</strong> <span style='color: #32CD32; font-weight: bold;'>Bs. " . number_format($totalAproximado, 2) . "</span>";
    }),

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
            



          // Información Regulares - Mejorada con todos los campos
Tables\Columns\TextColumn::make('regulares_info')
    ->label('🎟️ Regulares')
    ->html()
    ->getStateUsing(function ($record) {
        if (!$record->cantidad_regulares || $record->cantidad_regulares == 0) {
            return '<span style="color: gray;">No aplica</span>';
        }

        $rangoTickets = $record->regular_del && $record->regular_al 
            ? "{$record->regular_del} - {$record->regular_al}" 
            : 'No definido';

        $rangoFacturas = $record->rango_inicial_regular && $record->rango_final_regular
            ? "{$record->rango_inicial_regular} - {$record->rango_final_regular}"
            : 'No definido';

        $totalBoletos = $record->total_boletos_regulares ?? 0;
        $cantidadRestante = $record->cantidad_restante_regular ?? 0;
        $totalAproximado = $record->total_aproximado_bolivianos_regular ?? 0;

        // Color para Restantes (verde cuando está completo, cambia según cantidad)
        $colorRestantes = 'green'; // Completo por defecto
        if ($cantidadRestante <= 50) {
            $colorRestantes = 'red'; // Rojo cuando quedan 50 o menos
        } elseif ($cantidadRestante <= 100) {
            $colorRestantes = 'orange'; // Naranja cuando quedan 100 o menos
        } elseif ($cantidadRestante < $record->cantidad_regulares) {
            $colorRestantes = 'yellow'; // Amarillo cuando hay algunos usados
        }

        return "
        <strong>Cantidad Total:</strong> {$record->cantidad_regulares} talonarios<br>
        <strong>Restantes:</strong> <span style='color: {$colorRestantes}; font-weight: bold;'>{$cantidadRestante}</span><br>
        <strong>Del a Al:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoTickets}</span><br>
        <strong>Rango Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>{$rangoFacturas}</span><br>
        <strong>Total Tickets:</strong> <span style='color: #0084ffff; font-weight: bold;'>" . (int)$totalBoletos . "</span><br>
        <strong>Valor en Bs:</strong> <span style='color: #32CD32; font-weight: bold;'>Bs. " . number_format($totalAproximado, 2) . "</span>";
    }),

          // Círculo % regulares restantes
CircleProgress::make('regulares_circle')
    ->label('🏷️% Reg.')
    ->getStateUsing(fn($record) => [
        'total' => 100,
        'progress' => $record->cantidad_regulares > 0
            ? round((($record->cantidad_restante_regular ?? 0) / $record->cantidad_regulares) * 100)
            : 0,
    ]),

// Barra % regulares restantes
ProgressBar::make('regulares_progress_bar')
    ->label('🏷️% Restante Reg.')
    ->getStateUsing(fn($record) => [
        'total' => 100,
        'progress' => $record->cantidad_regulares > 0
            ? round((($record->cantidad_restante_regular ?? 0) / $record->cantidad_regulares) * 100)
            : 0,
    ]),


            // Tipo de talonarios
        Tables\Columns\BadgeColumn::make('tipo_estado')
    ->label('📊Tipo y Estado')
    ->getStateUsing(fn($record) => 
        // Texto combinado
        match($record->tipo_talonarios) {
            'preferenciales' => '🎫 Preferenciales',
            'regulares' => '🎟️ Regulares',
            'ambos' => '📘 Ambos',
            default => ucfirst($record->tipo_talonarios),
        } 
        . ' - ' . match($record->estado) {
            'disponible' => '✅ Disponible',
            'agotado' => '🔴 Agotado',
            'inactivo' => '⚫ Inactivo',
            default => ucfirst($record->estado),
        }
    )
    
    ->color(fn($record) => match(true) {
        $record->estado === 'agotado' => 'danger',
        $record->estado === 'inactivo' => 'secondary',
        $record->tipo_talonarios === 'preferenciales' => 'primary',
        $record->tipo_talonarios === 'regulares' => 'success',
        $record->tipo_talonarios === 'ambos' => 'warning',
        default => 'primary',
    })
    ->toggleable(isToggledHiddenByDefault: true),





           // Movimientos de talonarios
Tables\Columns\TextColumn::make('movimientos')
    ->label('📊 Movimientos')
    ->html()
    ->getStateUsing(function ($record) {
        return "
        <strong>Entregados:</strong> <span style='color: #32CD32; font-weight: bold;'>{$record->talonarios_entregados}</span><br>
        <strong>Vendidos:</strong> <span style='color: #0084ff; font-weight: bold;'>{$record->talonarios_vendidos}</span><br>
        <strong>Devueltos:</strong> <span style='color: #FFA500; font-weight: bold;'>{$record->talonarios_devueltos}</span>
        ";
    })
    ->toggleable(isToggledHiddenByDefault: true),

            // Información de CITE y gestión
            Tables\Columns\TextColumn::make('cite_info')
                ->label('📋 CITE/Gestión')
                ->html()
                ->getStateUsing(function ($record) {
                    return "
                    <strong>CITE:</strong> {$record->n_cite}<br>
                    <strong>Gestión:</strong> {$record->gestion}<br>
                    <strong>Dosificación:</strong> " . ($record->n_dosificacion ?: 'N/A') . "
                    ";
                })
                ->toggleable(isToggledHiddenByDefault: true),

           // Información de fechas completa
Tables\Columns\TextColumn::make('fechas_completas')
    ->label('📅 Fechas')
    ->html()
    ->getStateUsing(function ($record) {
        $formatDate = function ($date) {
            if (!$date) return 'N/A';
            return \Carbon\Carbon::parse($date)->format('d/m/Y');
        };

        $formatDateTime = function ($date) {
            if (!$date) return 'N/A';
            return \Carbon\Carbon::parse($date)->format('d/m/Y H:i');
        };

        return "
        <strong>Entrega:</strong> {$formatDate($record->fecha_entrega)}<br>
        <strong>Activación:</strong> {$formatDate($record->fecha_activacion)}<br>
        <strong>Solic. Dosif.:</strong> {$formatDate($record->fecha_solicitud_dosificacion)}<br>
        <strong>Autorización:</strong> {$formatDate($record->fecha_autorizacion)}<br>
        <strong>Creado:</strong> {$formatDateTime($record->created_at)}<br>
        <strong>Actualizado:</strong> {$formatDateTime($record->updated_at)}<br>
        <strong>Cierre:</strong> {$formatDate($record->fecha_cierre)}<br>
        ";
    })
    ->toggleable(isToggledHiddenByDefault: true),

            // Recaudación total
            Tables\Columns\TextColumn::make('total_recaudacion_bolivianos')
                ->label('💰 Recaudación Total')
                ->money('BOB')
                ->sortable()
                ->color('success')
                ->weight('bold')
                ->toggleable(isToggledHiddenByDefault: true)
                ->description(fn($record) => 
                    'Aprox: Bs. ' . number_format(
                        ($record->total_aproximado_bolivianos_preferencial ?? 0) + 
                        ($record->total_aproximado_bolivianos_regular ?? 0), 
                        2
                    )
                ),
               

            // Observaciones
            Tables\Columns\TextColumn::make('observaciones')
                ->label('📝 Observaciones')
                ->limit(50)
                ->tooltip(fn($record) => $record->observaciones)
                ->toggleable(isToggledHiddenByDefault: true),

        ])

        ->filters([ // <-- Asegúrate de que haya un -> antes de filters
            Tables\Filters\SelectFilter::make('cajero_id')
                ->label('👤 Cajero')
                ->relationship('cajero', 'nombre')
                ->searchable()
                ->preload(),

            Tables\Filters\SelectFilter::make('estado_preferencial')
                ->label('🏷️Estado Preferenciales')
                ->options([
                    '1' => 'Activo',
                    '0' => 'Inactivo',
                ]),

            Tables\Filters\SelectFilter::make('estado_regular')
                ->label('🏷️Estado Regulares')
                ->options([
                    '1' => 'Activo',
                    '0' => 'Inactivo',
                ]),

            Tables\Filters\Filter::make('fecha_entrega')
                ->label('📅Fecha de Entrega')
                ->form([
                    Forms\Components\DatePicker::make('fecha_desde')
                        ->label('Desde'),
                    Forms\Components\DatePicker::make('fecha_hasta')
                        ->label('Hasta'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['fecha_desde'], fn($q) => $q->whereDate('fecha_entrega', '>=', $data['fecha_desde']))
                        ->when($data['fecha_hasta'], fn($q) => $q->whereDate('fecha_entrega', '<=', $data['fecha_hasta']));
                }),

            
        ])
        ->filtersFormColumns(2)
        
       ->actions([
    Tables\Actions\ActionGroup::make([
        // Botón para ver PDF desde campo de la base de datos
      Tables\Actions\Action::make('ver_pdf')
            ->label('Ver PDF Nota')
            ->icon('heroicon-o-document-text')
            ->color('danger')
            ->url(function ($record) {
                if ($record->cite_nota_solicitud) {
                    // Si el archivo está en storage
                    return asset('storage/' . $record->cite_nota_solicitud);
                }
                return null;
            })
            ->openUrlInNewTab()
            ->hidden(fn($record) => !$record->cite_nota_solicitud),
            
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
        CreateAction::make(),
    
Action::make('pdf')
    ->label('GENERAR PDF')
    ->icon('heroicon-o-document-text')
    ->color('danger')
    ->url(route('inventario.pdf')) // Solo abre la ruta directamente
    ->openUrlInNewTab()
        ])


        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('cambiar_estado')
                    ->label('Cambiar Estado')
                    ->form([
                        Forms\Components\Select::make('estado')
                            ->options([
                                'disponible' => 'Disponible',
                                'agotado' => 'Agotado',
                                'inactivo' => 'Inactivo',
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each->update(['estado' => $data['estado']]);
                    }),
            ]),
        ])
        ->defaultSort('created_at', 'desc');

         
}

// Método auxiliar para obtener el estado del talonario
private static function getEstadoTalonario(?int $estado): string
{
    return match($estado) {
        1 => '<span style="color: green;">Disponible</span>',
        2 => '<span style="color: red;">Agotado</span>',
        3 => '<span style="color: orange;">En uso</span>',
        4 => '<span style="color: gray;">Inactivo</span>',
        default => '<span style="color: gray;">No definido</span>'
    };
}
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarioTalonarios::route('/'),
            'create' => Pages\CreateInventarioTalonarios::route('/create'),
            'edit' => Pages\EditInventarioTalonarios::route('/{record}/edit'),
        ];
    }
}