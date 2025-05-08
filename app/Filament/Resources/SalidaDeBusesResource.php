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
use App\Models\AsignacionDeBus;
use App\Models\RangoMantenimiento;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
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
                        return AsignacionDeBus::with('bus')->get()
                            ->pluck('bus.numero_bus', 'id_designacion_bus');
                    })
                    ->reactive()

                    // Cargar datos al abrir el formulario en modo edición
                    ->afterStateHydrated(function ($state, callable $set) {
                        if ($state) {
                            $asignacion = AsignacionDeBus::with(['anfitrion', 'conductor'])->find($state);

                            if ($asignacion) {
                                $anfitrion = $asignacion->anfitrion;
                                $conductor = $asignacion->conductor;

                                // Asigna los nombres del anfitrión y conductor al cargar el formulario
                                $set('anfitrion_nombre', $anfitrion ? "{$anfitrion->nombre} {$anfitrion->apellido_paterno} {$anfitrion->apellido_materno}" : 'No encontrado');
                                $set('conductor_nombre', $conductor ? "{$conductor->nombre} {$conductor->apellido_paterno} {$conductor->apellido_materno}" : 'No encontrado');
                            }
                        }
                    })

                    /* Busca los nombres de los anfitriones y conductores asignados */
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
                    /* REGLAS no puede salir un mismo bus el mismo dia*/
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, $fail) use ($get) {
                            $fecha = $get('fecha_salida');
                            $recordId = $get('id_salida_bus'); // ID del registro actual
                    
                            if (!$fecha) return;
                    
                            $existe = SalidaDeBuses::where('designacion_id', $value)
                                ->where('fecha_salida', $fecha)
                                ->when($recordId, function ($query) use ($recordId) {
                                    $query->where('id_salida_bus', '!=', $recordId);
                                })
                                ->exists();
                    
                            if ($existe) {
                                $fail("Ya existe una salida registrada para este bus en la fecha seleccionada.");
                            }
                        };
                    })
                    ->columnSpanFull(),
                /* Perosnal confirmado */
                Forms\Components\TextInput::make('anfitrion_nombre')
                    ->label('Anfitrión')
                    ->disabled()
                    ->default('Seleccione un bus'),

                Forms\Components\TextInput::make('conductor_nombre')
                    ->label('Conductor')
                    ->disabled()
                    ->default('Seleccione un bus'),
                Forms\Components\Checkbox::make('anfitrion_confirmado')
                    ->label('Anfitrión confirmado'),

                Forms\Components\Checkbox::make('conductor_confirmado')
                    ->label('Conductor confirmado'),

                Forms\Components\Select::make('estado_salida')
                    ->options([
                        'salida' => 'Salida',
                        'no_salida' => 'No Salida',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('motivo_no_salida')->nullable(),

                Forms\Components\DatePicker::make('fecha_salida')
                    ->required(),
                Forms\Components\TimePicker::make('hora_salida')
                    ->required(),

                /* Kilometraje */

                TextInput::make('kilometraje_salida')
                    ->placeholder('Ingrese el kilometraje actual del bus')
                    ->numeric()
                    ->required(),
                TextInput::make('kilometraje_llegada')
                    ->label('Kilometraje de Llegada')
                    ->placeholder('Ingrese el kilometraje al finalizar el recorrido')
                    ->numeric()
                    ->reactive()
                    ->lazy()
                    /* REGLA Kilometraje de llegada no puede ser menor que kilometraje de salida */
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



                // Toggle Buttons para el estado de mantenimiento
                ToggleButtons::make('status_mantenimiento')

                    ->label('¿Mantenimiento realizado?')
                    ->helperText('Marque esta opción si ya se realizó el mantenimiento correspondiente.')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'realizado' => 'Realizado',
                    ])
                    ->icons([
                        'pendiente' => 'heroicon-o-clock',
                        'realizado' => 'heroicon-o-check-circle',
                    ])
                    ->colors([
                        'pendiente' => 'warning',
                        'realizado' => 'success',
                    ])
                    ->default('pendiente') // Estado por defecto
                    ->reactive(),


                // Campo tipo de mantenimiento (solo lectura)
                Forms\Components\TextInput::make('tipo_mantenimiento')
                    ->label('Tipo de Mantenimiento')
                    ->placeholder('Se calculará automáticamente con el kilometraje de llegada')
                    ->readOnly()

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
                Tables\Columns\TextColumn::make('designacionBus.anfitrion.nombre'), // Display the name of the anfitrion
                Tables\Columns\TextColumn::make('designacionBus.conductor.nombre'), // Display the bus designation
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


                Tables\Columns\TextColumn::make('tipo_mantenimiento'),
                Tables\Columns\TextColumn::make('status_mantenimiento')
                    ->label('Mantenimiento')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {

                        'pendiente' => 'warning',
                        'realizado' => 'success',

                    })

            ])
            ->filters([
                //
            ])
            ->actions([
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
