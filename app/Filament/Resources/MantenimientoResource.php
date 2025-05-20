<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MantenimientoResource\Pages;
use App\Filament\Resources\MantenimientoResource\RelationManagers;
use App\Models\Mantenimiento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Bus;
use App\Models\Tecnico;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;

class MantenimientoResource extends Resource
{
    protected static ?string $model = Mantenimiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bus_id')
            ->relationship('bus', 'numero_bus')
            ->required()
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set) {
                $bus = Bus::find($state);
                if ($bus) {
                    $set('km_anterior', $bus->mantenimientos()->latest()->first()?->km_actual ?? 0);
                }
            }),

        Forms\Components\DatePicker::make('fecha_mantenimiento')
            ->required(),

        TextInput::make('km_anterior')
            ->numeric()
            ->readonly()
            ->default(0)
            ->label('Kilometraje Anterior')
            ->dehydrateStateUsing(fn($state) => $state ?: 0),

        TextInput::make('km_actual')
            ->numeric()
            ->required()
            ->reactive()
            ->lazy()
            ->label('Kilometraje Actual')
            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                $anterior = $get('km_anterior');
                $actual = $state;

                if (is_numeric($actual) && is_numeric($anterior)) {
                    $set('km_actual_recorrido', $actual - $anterior);

                    // Propuesta automática de tipo de mantenimiento
                    $tipoSugerido = Mantenimiento::calcularTipoPorKm($actual);
                    $set('tipo_mantenimiento', $tipoSugerido);
                }
            })
            ->rules([
                fn (callable $get) => function ($attribute, $value, $fail) use ($get) {
                    $anterior = $get('km_anterior');
                    if (is_numeric($value) && $value < $anterior) {
                        $fail("El kilometraje actual no puede ser menor al anterior ($anterior).");
                    }
                },
            ]),

        TextInput::make('km_actual_recorrido')
            ->numeric()
            ->readonly()
            ->required()
            ->label('Km Recorrido'),

        // Editable si el usuario quiere cambiarlo
        Forms\Components\Select::make('tipo_mantenimiento')
            ->label('Tipo de Mantenimiento')
            ->required()
            ->options([
                'L' => 'L',
                'MP1' => 'MP1',
                'MP2' => 'MP2',
                'MP3' => 'MP3',
                'MP4' => 'MP4',
                'MP5' => 'MP5',
            ])
            ->helperText('Seleccionado automáticamente según el kilometraje, pero puedes modificarlo.'),

        Forms\Components\Select::make('tecnico_id')
            ->label('Técnico Responsable')
            ->options(Tecnico::all()->mapWithKeys(fn($t) => [
                $t->id => "{$t->nombre} {$t->apellido_paterno} {$t->apellido_materno}"
            ]))
            ->searchable()
            ->required(),

        ToggleButtons::make('estado_mantenimiento')
            ->label('¿Mantenimiento realizado?')
            ->helperText('Marca si el mantenimiento ya se realizó.')
            ->options([
                'pendiente' => 'Pendiente',
                'en_proceso' => 'En Proceso',
                'realizado' => 'Realizado',
            ])
            ->icons([
                'pendiente' => 'heroicon-o-clock',
                'en_proceso' => 'heroicon-o-cog',
                'realizado' => 'heroicon-o-check-circle',
            ])
            ->colors([
                'pendiente' => 'warning',
                'en_proceso' => 'info',
                'realizado' => 'success',
            ])
            ->default('pendiente')
            ->required(),

        Textarea::make('observaciones')
            ->label('Observaciones')
            ->rows(3),

        TextInput::make('generado_por')
            ->label('Generado Por')
            ->default('manual')
            ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bus.numero_bus')->label('Bus'),
                Tables\Columns\TextColumn::make('fecha_mantenimiento')->date(),
                Tables\Columns\TextColumn::make('km_actual'),
                Tables\Columns\TextColumn::make('km_anterior'),
                Tables\Columns\TextColumn::make('km_actual_recorrido'),
                Tables\Columns\TextColumn::make('tipo_mantenimiento'),
                Tables\Columns\TextColumn::make('estado_mantenimiento')
                    ->label('Mantenimiento')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {

                        'pendiente' => 'warning',
                        'en_proceso' => 'info',
                        'realizado' => 'success',

                    }),
                Tables\Columns\TextColumn::make('generado_por'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMantenimientos::route('/'),
            'create' => Pages\CreateMantenimiento::route('/create'),
            'edit' => Pages\EditMantenimiento::route('/{record}/edit'),
        ];
    }
}
