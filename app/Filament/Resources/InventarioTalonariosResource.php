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

    protected static ?string $navigationLabel = 'Inventarios de Talonarios';
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
                        Grid::make(4)->schema([
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


                            Forms\Components\TextInput::make('cantidad_preferenciales')
                                ->label('Cantidad Preferenciales')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->numeric()
                                ->disabled() // ⛔ el usuario no puede editarlo
                                ->dehydrated(true)
                                ->reactive()
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
                                    $ultimo = \App\Models\InventarioTalonarios::orderByDesc('rango_final_preferencial')->first();
                                    return $ultimo ? $ultimo->rango_final_preferencial + 1 : 1;
                                }),

                        ]),
                    ])
                    ->visible(fn($get) => $get('show_preferenciales')),


                // Regulares
                Forms\Components\Section::make('REGULARES')
                    ->schema([
                        Grid::make(4)->schema([

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

                            Forms\Components\TextInput::make('cantidad_regulares')
                                ->label('Cantidad Regulares')
                                ->prefixIcon('heroicon-o-hashtag')
                                ->numeric()
                                ->disabled()              // ❌ No editable
                                ->dehydrated(true)        // ✅ Se guarda en la base de datos
                                ->reactive()
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
                    ->label('Resumen General')
                    ->html()
                    ->getStateUsing(function ($record) {
                        return "
                        <strong>N° CITE:</strong> {$record->n_cite}<br>
                        <strong>Gestión:</strong> {$record->gestion}<br>
                        <strong>N° Dosificación:</strong> {$record->n_dosificacion}<br>
                        <strong>Autorización:</strong> {$record->numero_autorizacion}<br>
                        <strong>F.Solicitud:</strong> {$record->fecha_solicitud_dosificacion}<br>
                        <strong>F.Autorización:</strong> {$record->fecha_autorizacion}<br>
                        <strong>F.Activación:</strong> {$record->fecha_activacion}
                        ";
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // 🟪 Preferenciales resumen
                Tables\Columns\TextColumn::make('preferenciales_info')
                    ->label('Resumen Preferenciales')
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
        <strong>Cantidad:</strong> {$record->cantidad_preferenciales}<br>
        <strong>Restante:</strong> <span style='color:" . ($record->cantidad_restante_preferencial < 800 ? 'red' : ($record->cantidad_restante_preferencial < 2000 ? 'green' : 'blue')) . "'>
        {$record->cantidad_restante_preferencial}</span><br>
        <strong>Rango:</strong> {$record->rango_inicial_preferencial} - {$record->rango_final_preferencial}<br>
        <strong>Tickets:</strong> {$record->total_boletos_preferenciales}<br>
        <strong>Recaudo:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_preferencial, 2, '.', ',') . "<br>
        <strong>Estado:</strong> <span style='color:{$estadoColor}'>{$estadoTexto}</span>";
                    }),
                ProgressBar::make('preferenciales_progress_bar')
                    ->label('% Restante Preferenciales')
                    ->getStateUsing(fn($record) => [
                        'total' => 100,
                        'progress' => round((($record->cantidad_restante_preferencial ?? 0) / ($record->cantidad_preferenciales ?: 1)) * 100)
                    ]),

                // 🟦 Regulares resumen
                Tables\Columns\TextColumn::make('regulares_info')
                    ->label('Resumen Regulares')
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
        <strong>Cantidad:</strong> {$record->cantidad_regulares}<br>
        <strong>Restante:</strong> <span style='color:" . ($record->cantidad_restante_regular < 800 ? 'red' : ($record->cantidad_restante_regular < 2000 ? 'green' : 'blue')) . "'>
        {$record->cantidad_restante_regular}</span><br>
        <strong>Rango:</strong> {$record->rango_inicial_regular} - {$record->rango_final_regular}<br>
        <strong>Tickets:</strong> {$record->total_boletos_regulares}<br>
        <strong>Recaudo:</strong> Bs. " . number_format($record->total_aproximado_bolivianos_regular, 2, '.', ',') . "<br>
        <strong>Estado:</strong> <span style='color:{$estadoColor}'>{$estadoTexto}</span>";
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
                    ->wrap(), // Permite que se ajuste y no ensanche la tabla
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
                Tables\Filters\Filter::make('cantidad_restante_preferencial')
                    ->label('Cantidad Restante Preferencial < 1000')
                    ->query(fn($query) => $query->where('cantidad_restante_preferencial', '<', 1000)),

                Tables\Filters\Filter::make('cantidad_restante_regular')
                    ->label('Cantidad Restante Regular < 1000')
                    ->query(fn($query) => $query->where('cantidad_restante_regular', '<', 1000)),

                Tables\Filters\Filter::make('monto_preferencial_alto')
                    ->label('Recaudo Preferencial > Bs. 5000')
                    ->query(fn($query) => $query->where('total_aproximado_bolivianos_preferencial', '>', 5000)),

                Tables\Filters\Filter::make('monto_regular_alto')
                    ->label('Recaudo Regular > Bs. 5000')
                    ->query(fn($query) => $query->where('total_aproximado_bolivianos_regular', '>', 5000)),

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
                    ->url(fn() => route('inventario.pdf'))
                    ->openUrlInNewTab(),
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