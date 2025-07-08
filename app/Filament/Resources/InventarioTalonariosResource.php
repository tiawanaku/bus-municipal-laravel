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
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;


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

                            Forms\Components\FileUpload::make('cite_nota_solicitud')
                                ->label('CITE Nota de Solicitud')
                                ->acceptedFileTypes(['application/pdf']) // solo permite PDF
                                ->maxSize(10240) // tamaño máximo en KB (10 MB en este caso)
                                ->storeFileNamesIn('cite_nota_solicitud_filename') // si deseas guardar el nombre original
                                ->directory('notas_solicitud') // carpeta dentro de storage/app/public/notas_solicitud
                                ->required(), // si es obligatorio

                            Forms\Components\TextInput::make('n_cite')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->label('Nº de Cite'),



                            TextInput::make('gestion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Gestión')
                                ->default(date('Y')) // Solo muestra el año actual
                                ->readOnly(),        // El usuario no puede editarlo (opcional)


                            Forms\Components\TextInput::make('n_dosificacion')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->label('Nº de Dosificaion'),

                            Forms\Components\TextInput::make('numero_autorizacion')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->label('Nº de Autorizacion'),

                            Forms\Components\DatePicker::make('fecha_solicitud_dosificacion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Fecha de solicitud de dosificacion'),

                            Forms\Components\DatePicker::make('fecha_autorizacion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Fecha de Autorizacion'),

                            Forms\Components\DatePicker::make('fecha_activacion')
                                ->prefixIcon('heroicon-o-calendar')
                                ->label('Fecha de Activacion'),


                        ]),
                    ])
                    ->columns(1),

                // Selección del tipo de talonario sin pregunta
                Forms\Components\Select::make('tipo_talonarios')
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
                        // Mostrar u ocultar secciones según la selección
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

                Grid::make(2)->schema([

                    Forms\Components\Select::make('cajero_id')
                        ->label('Custodio')
                        ->prefixIcon('heroicon-o-user')
                        ->options(function () {
                            return \App\Models\Cajero::where('tipo_cajero', 'principal')
                                ->get()
                                ->mapWithKeys(function ($cajero) {
                                    return [$cajero->id => $cajero->nombre_completo ?: 'Nombre no disponible'];
                                });
                        })
                        ->searchable()
                        ->required(),



                    Forms\Components\DatePicker::make('fecha_entrega')
                        ->label('Fecha de Entrega')
                        ->prefixIcon('heroicon-o-calendar')
                        ->default(now())
                        ->disabled()
                        ->dehydrated(true)
                        ->required(),
                ]),

                // Preferenciales
                Forms\Components\Section::make('PREFERENCIALES')
                    ->schema([
                        Grid::make(3)->schema([

                            Forms\Components\TextInput::make('preferencial_del')
                                ->label('Del')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric(),

                            Forms\Components\TextInput::make('preferencial_al')
                                ->label('Al')
                                ->prefixIcon('heroicon-o-arrow-up')
                                ->numeric(),

                            Forms\Components\TextInput::make('rango_inicial_preferencial')
                                ->label('Rango Inicial')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric()
                                ->default(function () {
                                    $ultimo = \App\Models\InventarioTalonarios::orderByDesc('rango_final_preferencial')->first();
                                    return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                                }),

                        ]),
                    ])
                    ->visible(fn($get) => $get('show_preferenciales')),


                // Regulares
                Forms\Components\Section::make('REGULARES')
                    ->schema([
                        Grid::make(3)->schema([

                            Forms\Components\TextInput::make('regular_del')
                                ->label('Del')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric(),

                            Forms\Components\TextInput::make('regular_al')
                                ->label('Al')
                                ->prefixIcon('heroicon-o-arrow-up')
                                ->numeric(),

                            Forms\Components\TextInput::make('rango_inicial_regular')
                                ->label('Rango Inicial')
                                ->prefixIcon('heroicon-o-arrow-down')
                                ->numeric()
                                ->default(function () {
                                    $ultimo = \App\Models\InventarioTalonarios::orderByDesc('rango_final_regular')->first();
                                    return $ultimo ? $ultimo->rango_final_regular + 1 : 1;
                                }),
                        ]),
                    ])
                    ->visible(fn($get) => $get('show_regulares')),

                // Observaciones
                Forms\Components\Section::make('OBSERVACIONES')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3),
                    ])
                    ->columns(1),
            ]);
    }


    public static function saving(InventarioTalonarios $record)
    {
        $inicioPref = request('rango_inicial_preferencial');
        $finPref = request('rango_final_preferencial');

        $inicioReg = request('rango_inicial_regular');
        $finReg = request('rango_final_regular');

        // Validar Preferenciales
        $existePref = \App\Models\InventarioTalonarios::where(function ($query) use ($inicioPref, $finPref) {
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
        }

        // Validar Regulares
        $existeReg = \App\Models\InventarioTalonarios::where(function ($query) use ($inicioReg, $finReg) {
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
        }
    }
    public function getNombreCompletoAttribute(): string
    {
        $nombre = $this->nombre ?? '';
        $apellidoPaterno = $this->apellido_paterno ?? '';
        $apellidoMaterno = $this->apellido_materno ?? '';

        $fullName = trim("{$nombre} {$apellidoPaterno} {$apellidoMaterno}");

        return $fullName ?: 'Nombre no disponible';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cajero_id')
                    ->label('Cajero')
                    ->getStateUsing(function ($record) {
                        $cajero = \App\Models\Cajero::find($record->cajero_id);
                        return $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // 🟪  resumen detos generales
                Tables\Columns\TextColumn::make('resumen_general')
                    ->label('📄 Resumen General')
                    ->html()
                    ->getStateUsing(function ($record) {
                        return "
<span>🔖 <strong>N° CITE:</strong> {$record->n_cite}</span><br>
<span>📅 <strong>Gestión:</strong> {$record->gestion}</span><br>
<span>🧾 <strong>N° Dosificación:</strong> {$record->n_dosificacion}</span><br>
<span>✅ <strong>Autorización:</strong> {$record->numero_autorizacion}</span><br>
<span>📆 <strong>F. Solicitud:</strong> {$record->fecha_solicitud_dosificacion}</span><br>
<span>📆 <strong>F. Autorización:</strong> {$record->fecha_autorizacion}</span><br>
<span>🚀 <strong>F. Activación:</strong> {$record->fecha_activacion}</span>";
                    })
                    ->toggleable(isToggledHiddenByDefault: true),


                // 🟪 Preferenciales resumen
                // 🟪 Preferenciales resumen
                Tables\Columns\TextColumn::make('preferenciales_info')
                    ->label('🎫 Preferenciales')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $estadoColor = match ($record->estado_preferencial) {
                            0 => 'red',
                            1 => 'green',
                            2 => 'blue',
                            default => 'black'
                        };

                        $estadoTexto = match ($record->estado_preferencial) {
                            0 => 'Asignado',
                            1 => 'Asignable',
                            2 => 'En espera',
                            default => 'Desconocido'
                        };

                        return "
<span>🔢 <strong>Cantidad:</strong> {$record->cantidad_preferenciales}</span><br>
<span>📉 <strong>Restante:</strong> <span style='color:" .
                            ($record->cantidad_restante_preferencial < 800 ? 'red' : ($record->cantidad_restante_preferencial < 2000 ? 'green' : 'blue')) . "'>
    {$record->cantidad_restante_preferencial}</span></span><br>
<span>🔁 <strong>Rango:</strong> {$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}</span><br>
<span>📅<strong>Del-Al :</strong> {$record->preferencial_del} - {$record->preferencial_al}<br>
<span>🎟️ <strong>Tickets:</strong> {$record->total_boletos_preferenciales}</span><br>
<span>💰 <strong>Recaudo:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_preferencial, 2, '.', ',') . "</span><br>
<span>📌 <strong>Estado:</strong> <span style='color:{$estadoColor}'>{$estadoTexto}</span></span>";
                    }),


                ProgressBar::make('preferenciales_progress_bar')
                    ->label('% Restante Preferenciales')
                    ->getStateUsing(fn($record) => [
                        'total' => 100,
                        'progress' => round((($record->cantidad_restante_preferencial ?? 0) / ($record->cantidad_preferenciales ?: 1)) * 100)
                    ]),

                // 🟦 Regulares resumen
                Tables\Columns\TextColumn::make('regulares_info')
                    ->label('🎟️ Regulares')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $estadoColor = match ($record->estado_regular) {
                            0 => 'red',
                            1 => 'green',
                            2 => 'blue',
                            default => 'black'
                        };

                        $estadoTexto = match ($record->estado_regular) {
                            0 => 'Asignado',
                            1 => 'Asignable',
                            2 => 'En espera',
                            default => 'Desconocido'
                        };

                        return "
<span>🔢 <strong>Cantidad:</strong> {$record->cantidad_regulares}</span><br>
<span>📉 <strong>Restante:</strong> <span style='color:" .
                            ($record->cantidad_restante_regular < 800 ? 'red' : ($record->cantidad_restante_regular < 2000 ? 'green' : 'blue')) . "'>
    {$record->cantidad_restante_regular}</span></span><br>
<span>🔁 <strong>Rango:</strong> {$record->rango_inicial_regular} - {$record->rango_final_regular}</span><br>
<span>📅<strong>Del-Al:</strong> {$record->regular_del} - {$record->regular_al}<br>
<span>🎟️ <strong>Tickets:</strong> {$record->total_boletos_regulares}</span><br>
<span>💰 <strong>Recaudo:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_regular, 2, '.', ',') . "</span><br>
<span>📌 <strong>Estado:</strong> <span style='color:{$estadoColor}'>{$estadoTexto}</span></span>";
                    }),

                ProgressBar::make('regulares_progress_bar')
                    ->label('% Restante Regulares')
                    ->getStateUsing(fn($record) => [
                        'total' => 100,
                        'progress' => round((($record->cantidad_restante_regular ?? 0) / ($record->cantidad_regulares ?: 1)) * 100)
                    ]),

                // Observaciones si existen
                Tables\Columns\TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true), // Permite que se ajuste y no ensanche la tabla
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('cajero_id')
                    ->label('Cajero')
                    ->options(
                        Cajero::where('tipo_cajero', 'principal')
                            ->get()
                            ->mapWithKeys(fn($cajero) => [
                                $cajero->id => $cajero->nombre_completo ?: 'Nombre no disponible'
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
                Tables\Filters\SelectFilter::make('estado_preferencial')
                    ->label('Estados')
                    ->options([
                        0 => 'Asignado',
                        1 => 'Asignable',
                        2 => 'En Espera',
                    ]),

                // Solo este filtro tiene formulario (rango de fechas)
                Tables\Filters\Filter::make('fecha_creacion')
                    ->label('Rango de Fecha')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('from')->label('Desde'),
                            Forms\Components\DatePicker::make('until')->label('Hasta'),
                        ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($query) => $query->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($query) => $query->whereDate('created_at', '<=', $data['until']));
                    }),

                // NUEVOS FILTROS AÑADIDOS

                Tables\Filters\Filter::make('n_cite')
                    ->label('Buscar por Nº de CITE')
                    ->form([
                        TextInput::make('n_cite')
                            ->label('Número de CITE')
                            ->placeholder('Ej: GAM/UR/123/2024'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['n_cite'],
                                fn($q) => $q->where('n_cite', 'like', '%' . $data['n_cite'] . '%')
                                    ->orWhere('cite_nota_solicitud', 'like', '%' . $data['n_cite'] . '%')
                            );
                    }),


                Tables\Filters\Filter::make('gestion')
                    ->label('Filtrar por Gestión')
                    ->form([
                        Forms\Components\TextInput::make('gestion')
                            ->label('Gestión')
                            ->placeholder('Ej: 2024'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['gestion'], fn($query) => $query->where('gestion', 'like', '%' . $data['gestion'] . '%'));
                    }),


            ])
            ->filtersFormColumns(2)

            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('warning') // Amarillo para editar

                    ,
                    Tables\Actions\Action::make('descargar_pdf')
                        ->label('Hoja de Ruta en PDF')
                        ->url(fn($record) => asset('storage/' . $record->cite_nota_solicitud))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-document')
                        ->color('primary') // Azul para descargar
                        ->visible(fn($record) => !empty($record->cite_nota_solicitud)),
                ])
                    ->icon('heroicon-o-bars-3')
                    ->label('Opciones') // Texto del botón del grupo
            ])

            ->headerActions([
                CreateAction::make(),
                Tables\Actions\Action::make('descargar_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-printer')
                    ->url(function ($livewire) {
                        $filters = $livewire->tableFilters ?? [];
                        $queryParams = [];

                        if (isset($filters['estado_preferencial']['value']) && $filters['estado_preferencial']['value'] !== null) {
                            $queryParams['estado_preferencial'] = $filters['estado_preferencial']['value'];
                        }

                        return route('inventario.pdf', $queryParams);
                    })
                    ->openUrlInNewTab()
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
            'index' => Pages\ListInventarioTalonarios::route('/'),
            'create' => Pages\CreateInventarioTalonarios::route('/create'),
            'edit' => Pages\EditInventarioTalonarios::route('/{record}/edit'),
        ];
    }
}