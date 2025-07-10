<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalidaDeBusesResource\Pages;
use App\Models\SalidaDeBuses;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Form; // Directly import the Form class
use Filament\Resources\Table;
use Filament\Tables;
use App\Models\Anfitrion;
use App\Models\Ruta;
use App\Models\Conductor;
use App\Models\AsignacionDeBus;
use App\Models\RangoMantenimiento;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\Action;

use Closure;




class SalidaDeBusesResource extends Resource
{
    protected static ?string $model = SalidaDeBuses::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Gestión de Buses';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                /* Buses con asignacion previa */
                Forms\Components\Select::make('designacion_id')
                    ->label('Número de Bus')
                    ->options(function () {
                        $hoy = Carbon::today();

                        return AsignacionDeBus::with('bus')
                            ->whereDate('fecha_designacion', '<=', $hoy)
                            ->whereDate('fin_designacion', '>=', $hoy)
                            ->get()
                            ->pluck('bus.numero_bus', 'id_designacion_bus');
                    })
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set) {
                        if ($state) {
                            $asignacion = AsignacionDeBus::with(['anfitrion', 'conductor'])->find($state);

                            if ($asignacion) {
                                $anfitrion = $asignacion->anfitrion;
                                $conductor = $asignacion->conductor;

                                $set('anfitrion_nombre', $anfitrion ? "{$anfitrion->nombre} {$anfitrion->apellido_paterno} {$anfitrion->apellido_materno}" : 'No encontrado');
                                $set('conductor_nombre', $conductor ? "{$conductor->nombre} {$conductor->apellido_paterno} {$conductor->apellido_materno}" : 'No encontrado');
                            }
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        $asignacion = AsignacionDeBus::with(['anfitrion', 'conductor'])->find($state);

                        if ($asignacion) {
                            $anfitrion = $asignacion->anfitrion;
                            $conductor = $asignacion->conductor;

                            $set('anfitrion_nombre', $anfitrion ? "{$anfitrion->nombre} {$anfitrion->apellido_paterno} {$anfitrion->apellido_materno}" : 'No encontrado');
                            $set('conductor_nombre', $conductor ? "{$conductor->nombre} {$conductor->apellido_paterno} {$conductor->apellido_materno}" : 'No encontrado');
                        } else {
                            $set('anfitrion_nombre', 'No encontrado');
                            $set('conductor_nombre', 'No encontrado');
                        }
                    })
                    ->required()
                    /* Evitando que se registra más de una salida de un bus en un turno */
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, $fail) use ($get) {
                            $fecha = $get('fecha_salida');
                            $horarioId = $get('horario_id');
                            $recordId = $get('id_salida_bus');

                            if (!$fecha || !$horarioId) {
                                return; // Si no hay fecha o horario no validamos aún
                            }

                            // Verificar si ya existe una salida con mismo bus, fecha y horario, ignorando el actual (en edición)
                            $existe = SalidaDeBuses::where('designacion_id', $value)
                                ->where('fecha_salida', $fecha)
                                ->where('horario_id', $horarioId)
                                ->when($recordId, fn($query) => $query->where('id_salida_bus', '!=', $recordId))
                                ->exists();

                            if ($existe) {
                                $fail("Ya existe una salida registrada para este bus en la fecha y turno seleccionados.");
                            }
                        };
                    })
                    ->columnSpanFull(),

                /* Perosnal confirmado */
                Forms\Components\Section::make('Confirmación de personal')
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('anfitrion_nombre')
                                    ->label('Anfitrión')
                                    ->disabled()
                                    ->default('Seleccione un bus'),
                                Forms\Components\Checkbox::make('anfitrion_confirmado')
                                    ->label('Anfitrión confirmado')
                                    ->default(true)
                                    ->reactive(),

                                Forms\Components\Select::make('anfitrion_manual')
                                    ->label('Seleccionar Anfitrión Suplente')
                                    ->options(fn() => Anfitrion::all()->pluck('nombre', 'id')->toArray())
                                    ->visible(fn(callable $get) => !$get('anfitrion_confirmado'))
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Seleccione un anfitrión')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->suplantacionAnfitrion) {
                                            $component->state($record->suplantacionAnfitrion->id_anfitrion_suplente);
                                        }
                                    }),

                                Forms\Components\Textarea::make('motivo_anfitrion')
                                    ->label('Indique el motivo por el cual el anfitrión no se presentó')
                                    ->visible(fn(callable $get) => !$get('anfitrion_confirmado'))
                                    ->required()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->suplantacionAnfitrion) {
                                            $component->state($record->suplantacionAnfitrion->motivo);
                                        }
                                    }),



                            ])
                            ->columns(2), // Opcional, para que se vea en dos columnas
                        Forms\Components\Card::make()
                            ->schema([

                                Forms\Components\TextInput::make('conductor_nombre')
                                    ->label('Conductor')
                                    ->disabled()
                                    ->default('Seleccione un bus'),



                                Forms\Components\Checkbox::make('conductor_confirmado')
                                    ->default(true)
                                    ->label('Conductor confirmado')
                                    ->reactive(),

                                Forms\Components\Select::make('conductor_manual')
                                    ->label('Seleccionar Conductor Suplente')
                                    ->options(fn() => Conductor::all()->pluck('nombre', 'id')->toArray())
                                    ->visible(fn(callable $get) => !$get('conductor_confirmado'))
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Seleccione un conductor')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->suplantacionConductor) {
                                            $component->state($record->suplantacionConductor->id_conductor_suplente);
                                        }
                                    }),
                                Forms\Components\Textarea::make('motivo_conductor')
                                    ->label('Indique el motivo por el cual el conductor no se presento')
                                    ->visible(fn(callable $get) => !$get('conductor_confirmado'))
                                    ->required()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->suplantacionConductor) {
                                            $component->state($record->suplantacionConductor->motivo);
                                        }
                                    }),
                            ])
                            ->columns(2),
                    ]),
                Forms\Components\Section::make('Datos de la Salida')

                    ->schema([
                        Forms\Components\Select::make('estado_salida')
                            ->options([
                                'salida' => 'Salida',
                                'no_salida' => 'No Salida',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\Textarea::make('motivo_no_salida')->required()
                            ->visible(fn($get) => $get('estado_salida') === 'no_salida'),
                        /* Ruta */
                        Select::make('ruta_id')
                            ->label('Ruta')
                            ->options(Ruta::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->visible(fn($get) => $get('estado_salida') === 'salida'),

                        Forms\Components\DatePicker::make('fecha_salida')
                            ->required()
                            ->default(Carbon::now())
                            ->lazy()
                            ->afterStateUpdated(
                                fn(callable $set, callable $get) =>
                                $set(
                                    'kilometraje_salida',
                                    SalidaDeBuses::where('designacion_id', $get('designacion_id'))
                                        ->where('fecha_salida', '<', $get('fecha_salida'))
                                        ->orderByDesc('fecha_salida')
                                        ->orderByDesc('hora_salida')
                                        ->value('kilometraje_llegada') ?? 0
                                )
                            )
                            ->disabled()
                            ->dehydrated(true),
                        Forms\Components\TimePicker::make('hora_salida')
                            ->required()
                            ->default(Carbon::now()->format('H:i'))
                            ->disabled()
                            ->dehydrated(true),

                        Select::make('horario_id')
                            ->label('Horario / Turno')
                            ->required()
                            ->relationship('horario', 'turno')
                            ->searchable()
                            ->preload()
                            ->default(fn() => \App\Models\Horario::obtenerHorarioActual()?->id)
                            ->helperText('Seleccione el turno correspondiente'),
                        /* Kilometraje de salida */


                        TextInput::make('kilometraje_salida')
                            ->placeholder('Ingrese el kilometraje actual del bus')
                            ->numeric()
                            ->required()
                            /* REGLA: El kilometraje de salida NO puede ser menor que el último kilometraje de llegada */
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, $fail) use ($get) {
                                    $designacionId = $get('designacion_id');
                                    $fechaSalida = $get('fecha_salida');
                                    $horaSalida = $get('hora_salida');

                                    if (!$designacionId || is_null($value) || !$fechaSalida) {
                                        return;
                                    }

                                    // Obtener el bus_id asociado a esta designacion
                                    $designacion = AsignacionDeBus::find($designacionId);
                                    if (!$designacion) {
                                        return;
                                    }
                                    $busId = $designacion->bus_id;

                                    if (!$busId) {
                                        return;
                                    }

                                    // Buscar la última salida para el mismo bus (independientemente de designacion)
                                    // con fecha y hora menor que la actual (para comparar correctamente)
                                    $ultimoRegistro = SalidaDeBuses::whereHas('asignaciones', function ($query) use ($busId) {
                                        $query->where('bus_id', $busId);
                                    })
                                        ->where(function ($query) use ($fechaSalida, $horaSalida) {
                                        $query->where('fecha_salida', '<', $fechaSalida)
                                            ->orWhere(function ($q) use ($fechaSalida, $horaSalida) {
                                                $q->where('fecha_salida', '=', $fechaSalida)
                                                    ->where('hora_salida', '<', $horaSalida);
                                            });
                                    })
                                        ->orderByDesc('fecha_salida')
                                        ->orderByDesc('hora_salida')
                                        ->first();

                                    if ($ultimoRegistro && $value < $ultimoRegistro->kilometraje_llegada) {
                                        $fail('El kilometraje de salida no puede ser menor que el último kilometraje de llegada registrado para este bus (' . $ultimoRegistro->kilometraje_llegada . ').');
                                    }
                                };

                            }),

                        /* Observaciones */
                        Textarea::make('observaciones')


                    ])
                    ->columns(2),




                Section::make('Datos de la llegada')
                    ->visible(fn($livewire) => filled($livewire->getRecord())) // ← Visible solo si hay un registro (modo edición)
                    ->schema([
                        TextInput::make('kilometraje_llegada')
                            ->label('Kilometraje de Llegada')
                            ->placeholder('Ingrese el kilometraje al finalizar el recorrido')
                            ->numeric()
                            ->reactive()
                            ->lazy()
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, $fail) use ($get) {
                                    $kmSalida = $get('kilometraje_salida');
                                    if (!is_null($kmSalida) && $value < $kmSalida) {
                                        $fail('El kilometraje de llegada no puede ser menor que el de salida.');
                                    }
                                };
                            })
                            ->afterStateUpdated(function ($set, $state) {
                                $rango = RangoMantenimiento::where('km_min', '<=', $state)
                                    ->where('km_max', '>=', $state)
                                    ->first();

                                $set('tipo_mantenimiento', $rango ? $rango->tipo_mantenimiento : 'No aplica');
                            }),

                        DatePicker::make('fecha_llegada')
                            ->label('Fecha de Llegada')
                            ->disabled()
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($set, $get) {
                                if (is_null($get('fecha_llegada'))) {
                                    $set('fecha_llegada', Carbon::now()->format('Y-m-d'));
                                }
                            }),

                        TimePicker::make('hora_llegada')
                            ->label('Hora de Llegada')
                            ->disabled()
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($set, $get) {
                                if (is_null($get('hora_llegada'))) {
                                    $set('hora_llegada', Carbon::now()->format('H:i'));
                                }
                            }),

                        Textarea::make('observaciones'),
                        // Campo tipo de mantenimiento (solo lectura)
                        Forms\Components\TextInput::make('tipo_mantenimiento')
                            ->label('Tipo de Mantenimiento')

                            ->readOnly(),
                    ]),

            ]);
    }



    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('designacionBus.bus.numero_bus')
                    ->label('N° Bus'),

                Tables\Columns\TextColumn::make('fecha_salida'),
                Tables\Columns\TextColumn::make('hora_salida'),
                Tables\Columns\TextColumn::make('designacionBus.anfitrion.nombre')
                    ->toggleable(isToggledHiddenByDefault: true), // Display the name of the anfitrion
                Tables\Columns\TextColumn::make('designacionBus.conductor.nombre')
                    ->toggleable(isToggledHiddenByDefault: true), // Display the bus designation
                Tables\Columns\IconColumn::make('estado_salida')
                    ->label('Estado de Salida')
                    ->options([
                        'heroicon-o-check-badge' => 'salida',
                        'heroicon-o-x-mark' => 'no_salida',
                    ])
                    ->colors([
                        'success' => 'salida',
                        'danger' => 'no_salida',
                    ]),


                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->getStateUsing(function ($record) {
                        if ($record->estado_salida === 'no_salida') {
                            return 'No salida';
                        }

                        if (!is_null($record->fecha_llegada) && !is_null($record->hora_llegada)) {
                            return 'En patio';
                        }

                        return 'En ruta';
                    })

                    ->tooltip(function ($record) {
                        if (!is_null($record->fecha_llegada) && !is_null($record->hora_llegada)) {
                            $fecha = Carbon::parse($record->fecha_llegada)->format('d/m/Y');
                            return "Llegó el {$fecha} a las {$record->hora_llegada}";
                        }

                        return 'Sin llegada registrada';
                    })
                    ->colors([
                        'danger' => 'No salida',   // rojo para no salida
                        'success' => 'En patio',    // verde para En patio
                        'warning' => 'En ruta',    // amarillo para en ruta
                    ]),
                Tables\Columns\TextColumn::make('ruta.nombre')
                    ->label('Ruta'),

                Tables\Columns\TextColumn::make('kilometraje_salida')
                    ->label('KM de Salida')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kilometraje_llegada')
                    ->label('KM de Llegada')
                    ->toggleable(isToggledHiddenByDefault: true),


            ])

            ->filters([
                //
            ])
            ->actions([
                Action::make('marcarLlegada')
                    ->label('Marcar llegada')
                    ->icon('heroicon-o-check-circle')

                    ->form([
                        Forms\Components\TextInput::make('kilometraje_llegada')
                            ->label('Kilometraje de llegada')
                            ->required()
                            ->numeric()
                            ->lazy()
                            ->rule(function (callable $get, $record) {
                                return function (string $attribute, $value, $fail) use ($record) {
                                    if ($value < $record->kilometraje_salida) {
                                        $fail('El kilometraje de llegada no puede ser menor que el kilometraje de salida.');
                                    }
                                };
                            })
                            ->afterStateUpdated(function ($set, $state) {
                                $rango = RangoMantenimiento::where('km_min', '<=', $state)
                                    ->where('km_max', '>=', $state)
                                    ->first();

                                $set('tipo_mantenimiento', $rango ? $rango->tipo_mantenimiento : 'No aplica');
                            }),

                        Forms\Components\TextInput::make('tipo_mantenimiento')
                            ->label('Tipo de mantenimiento')
                            ->readOnly() // Solo lectura

                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'fecha_llegada' => now()->toDateString(),
                            'hora_llegada' => now()->format('H:i'),
                            'kilometraje_llegada' => $data['kilometraje_llegada'],
                            'tipo_mantenimiento' => $data['tipo_mantenimiento'],
                        ]);
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    /* Visible solo para aquellos que registraron su salida */


                    ->visible(
                        fn($record) =>
                        auth()->user()?->can('marcar-llegada') &&
                        !$record->fecha_llegada &&
                        !$record->hora_llegada &&
                        $record->estado_salida !== 'no_salida'
                    ),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalidaDeBuses::route('/'),
            'create' => Pages\CreateSalidaDeBuses::route('/create'),
            'edit' => Pages\EditSalidaDeBuses::route('/{record}/edit'),
        ];
    }



}
