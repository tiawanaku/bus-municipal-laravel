<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignacionDeBusResource\Pages;
use App\Models\AsignacionDeBus;
use Filament\Forms;
use Filament\Forms\Form; // Asegúrate de usar este espacio de nombres
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\DateTimeColumn; // Cambiado a DateTimeColumn
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select as FilamentSelect; // Cambiamos el alias aquí


use Filament\Support\Exceptions\Halt;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;
use Filament\Tables\Columns\IconColumn;
use App\Models\Bus;
use App\Models\Horario;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Container; // Asegúrate de que esta línea esté presente
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\CheckboxList;


class AsignacionDeBusResource extends Resource
{
    protected static ?string $model = AsignacionDeBus::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck'; // Usa otro ícono como ejemplo

protected static ?string $navigationGroup = 'Gestión de Buses';
    protected static ?string $navigationLabel = 'Asignación de Buses';
    protected static ?string $pluralModelLabel = 'Asignaciones de Bus';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            /* Mostrar un form simple si es para editar */
            Group::make()
                ->hidden(fn(callable $get) => $get('id_designacion_bus') === null) // Ocultar si estamos creando
                ->schema([
                    TextInput::make('id_buses')
                        ->label('ID del Bus')
                        ->numeric()
                        ->hidden(),

                    Placeholder::make('numero_bus')
                        ->label('Número de Bus')
                        ->content(function (callable $get) {
                            $busId = $get('id_buses');
                            $bus = Bus::find($busId);
                            return $bus ? $bus->numero_bus : 'No existe bus con ese ID';
                        }),
                    TextInput::make('id_anfitrion')
                        ->label('Nombre de Anfitrión')
                        ->numeric()
                        ->hidden(),
                    Placeholder::make('nombre_completo_anfitrion')
                        ->label('Nombre completo del Anfitrión')
                        ->content(function (callable $get) {
                            $anfitrionId = $get('id_anfitrion');
                            $anfitrion = \App\Models\Anfitrion::find($anfitrionId);

                            return $anfitrion ? $anfitrion->full_name : 'No asignado';
                        }),
                    TextInput::make('id_conductor')
                        ->label('Nombre de Conductor')
                        ->numeric()
                        ->hidden(),
                    Placeholder::make('nombre_completo_conductor')
                        ->label('Nombre completo del Conductor')
                        ->content(function (callable $get) {
                            $conductorId = $get('id_conductor');
                            $conductor = \App\Models\Conductor::find($conductorId);

                            return $conductor ? $conductor->nombre_completo : 'No asignado';
                        }),

                    TextInput::make('n_ficha')
                        ->label('Número de Ficha')
                        ->numeric()
                        ->required(),
                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3)
                        ->nullable(),
                ]),
            // Hacer que los demás campos solo sean visibles en creación
            Group::make()
                ->hidden(fn(callable $get) => $get('id_designacion_bus') !== null) // Ocultar si estamos en edición
                ->schema([
                    Wizard::make([
                        Wizard\Step::make('Seleccionar fecha de asignación')
                            ->schema([
                                // ...
                                Group::make()
                                    ->schema([

                                        Select::make('tipo_asignacion')
                                            ->label('Tipo de asignación')
                                            ->options([
                                                'dia' => 'Un día',
                                                'semana' => 'Una semana',
                                                'mes' => 'Un mes',
                                                'personalizado' => 'Personalizado',
                                            ])
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                                $inicio = $get('fecha_designacion');

                                                if (!$inicio || !$state)
                                                    return;

                                                $fecha = Carbon::parse($inicio);

                                                switch ($state) {
                                                    case 'dia':
                                                        $set('fin_designacion', $fecha->format('Y-m-d'));
                                                        break;

                                                    case 'semana':
                                                        $lunes = $fecha->copy()->startOfWeek();
                                                        $viernes = $lunes->copy()->addDays(4);
                                                        $set('fin_designacion', $viernes->format('Y-m-d'));
                                                        break;

                                                    case 'mes':
                                                        $set('fin_designacion', $fecha->copy()->endOfMonth()->format('Y-m-d'));
                                                        break;

                                                    case 'personalizado':
                                                        break;
                                                }
                                            })
                                    ])
                                    ->columns(1),
                                   

                                Grid::make(2)->schema([
                                    DatePicker::make('fecha_designacion')
                                        ->label('Fecha de inicio')
                                        ->minDate(now()->startOfDay())
                                        ->validationMessages([
                                            'after_or_equal' => 'No se permiten fechas pasadas. Elige una fecha válida a partir de hoy.',
                                        ])
                                        ->required()
                                        ->disabled(fn(callable $get) => !$get('tipo_asignacion'))
                                        ->reactive()
                                        ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                            $tipo = $get('tipo_asignacion');
                                            if (!$state || !$tipo)
                                                return;

                                            $inicio = Carbon::parse($state);
                                            switch ($tipo) {
                                                case 'dia':
                                                    $set('fin_designacion', $inicio->format('Y-m-d'));
                                                    break;
                                                case 'semana':
                                                    $set('fin_designacion', $inicio->copy()->startOfWeek()->addDays(4)->format('Y-m-d'));
                                                    break;
                                                case 'mes':
                                                    $set('fin_designacion', $inicio->copy()->endOfMonth()->format('Y-m-d'));
                                                    break;
                                            }

                                            // Verificar disponibilidad justo después de cambiar fechas
                                            $fechaInicio = $get('fecha_designacion');
                                            $fechaFin = $get('fin_designacion');

                                            if (!$fechaInicio || !$fechaFin)
                                                return;

                                            $disponibles = AsignacionDeBus::buscarDisponibilidad($fechaInicio, $fechaFin);

                                            // Guardar directamente los arrays de datos
                                            $set('conductores_disponibles', $disponibles['conductores']->isEmpty() ? [] : $disponibles['conductores']->toArray());
                                            $set('anfitriones_disponibles', $disponibles['anfitriones']->isEmpty() ? [] : $disponibles['anfitriones']->toArray());
                                            $set('buses_disponibles', $disponibles['buses']->isEmpty() ? [] : $disponibles['buses']->toArray());


                                        }),
                                    DatePicker::make('fin_designacion')
                                        ->label('Fecha de fin')
                                        ->afterOrEqual('fecha_designacion')
                                        ->required()
                                        ->reactive()
                                        /* si el tipo es personalizado se permite seleccionar la fecha fin */
                                        ->disabled(fn(callable $get) => $get('tipo_asignacion') !== 'personalizado')
                                        ->dehydrated()

                                        ->validationMessages([
                                            'after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de designación.',
                                        ])
                                ])

                            ])
                        ,
                        Wizard\Step::make('Validando disponibilidad')
                            ->schema([

                                Placeholder::make('mensaje_disponibilidad')

                                    ->label('Estado de disponibilidad')
                                    ->reactive()
                                    ->content(function (callable $get) {
                                        $conductores = $get('conductores_disponibles');
                                        $anfitriones = $get('anfitriones_disponibles');
                                        $buses = $get('buses_disponibles');


                                        $mensajes = [];

                                        if (empty($conductores)) {
                                            $mensajes[] = '❌ No hay conductores disponibles.';
                                        }
                                        if (empty($anfitriones)) {
                                            $mensajes[] = '❌ No hay anfitriones disponibles.';
                                        }
                                        if (empty($buses)) {
                                            $mensajes[] = '❌ No hay buses disponibles.';
                                        }

                                        // Si alguno falta, se muestra advertencia
                                        if (!empty($mensajes)) {
                                            return implode("\n", $mensajes) . "\n ⚠️ No hay disponibilidad de conductores, anfitriones o buses en el período seleccionado. Por favor, elige otro rango de fechas.";
                                        }

                                        return '✅ Hay disponibilidad de conductores, anfitriones y buses. Puedes continuar.';
                                    }),

                            ])
                            ->beforeValidation(function (callable $get) {
                                $conductores = $get('conductores_disponibles');
                                $anfitriones = $get('anfitriones_disponibles');
                                $buses = $get('buses_disponibles');

                                // 🔹 Si ningún recurso está disponible, bloquear avance
                                if (empty($conductores) || empty($anfitriones) || empty($buses)) {
                                    throw new Halt();
                                }
                            }),
                        Wizard\Step::make('Asignación')
                            ->schema([
                                // ...
                                Grid::make()->columns(2)->schema([
                                    Group::make()
                                        ->schema([
                                            FilamentSelect::make('id_conductor')
                                                ->label('Conductor')
                                                ->options(fn(callable $get) => $get('conductores_disponibles'))
                                                ->reactive()
                                                ->preload()
                                                ->required()
                                                ->placeholder('Selecciona un conductor'),
                                            FilamentSelect::make('id_anfitrion')
                                                ->label('Anfitrión')
                                                ->options(fn(callable $get) => $get('anfitriones_disponibles'))
                                                ->reactive()
                                                ->preload()
                                                ->required()
                                                ->placeholder('Selecciona un anfitrión'),

                                            FilamentSelect::make('id_buses')
                                                ->label('Bus')
                                                ->options(fn(callable $get) => $get('buses_disponibles'))
                                                ->reactive()
                                                ->required()
                                                ->placeholder('Selecciona un bus'),
                                        ])
                                        ->columnSpan(1)
                                        ->extraAttributes([
                                            'class' => 'p-6 min-h-[250px] bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg border border-transparent text-white'
                                        ]),

                                    Group::make()
                                        ->schema([
                                            TextInput::make('n_ficha')
                                                ->label('Número de Ficha')
                                                ->numeric()
                                                ->required()
                                                ->placeholder('Ingrese el número de ficha'),

                                            Textarea::make('observaciones')
                                                ->label('Observaciones')
                                                ->rows(3)
                                                ->nullable()
                                                ->placeholder('Ingrese observaciones adicionales...'),
                                        ])
                                        ->columnSpan(1)
                                        ->extraAttributes(['class' => 'p-6 min-h-[250px] bg-gradient-to-r from-green-500 to-green-700 rounded-xl shadow-lg border border-transparent text-white']), // Fondo degradado y bordes redondeados
                                ]),
                            ]),

                    ])->columnSpanFull()

                ])->columnSpanFull()
        ]);
    }
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('conductor.nombre_completo') // Usa el método que devuelve el nombre completo
                    ->label('Conductor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bus.numero_bus')
                    ->label('Bus')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->limit(50),

                IconColumn::make('asignacion_activa')
                    ->label('Asignación activa')
                    ->boolean()
                    ->getStateUsing(
                        fn($record) =>
                        now()->between(
                            Carbon::parse($record->fecha_designacion)->startOfDay(),
                            Carbon::parse($record->fin_designacion ?? now()->addYear())->endOfDay()
                        )
                    )
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),


                TextColumn::make('fecha_designacion') // Usando DateTimeColumn
                    ->label('Fecha de Designación')
                    ->sortable(),

                TextColumn::make('fin_designacion')
                    ->label('Fecha Fin Designación')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsignacionDeBuses::route('/'),
            'create' => Pages\CreateAsignacionDeBus::route('/create'),
            'edit' => Pages\EditAsignacionDeBus::route('/{record}/edit'),
        ];
    }
}