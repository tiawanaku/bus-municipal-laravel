<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioTalonariosResource\Pages;
use App\Filament\Resources\InventarioTalonariosResource\RelationManagers;
use App\Models\InventarioTalonarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventarioTalonariosResource extends Resource
{
    protected static ?string $model = InventarioTalonarios::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Inventario de Talonarios';

  public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Selección del tipo de talonario sin pregunta
            Forms\Components\Select::make('tipo_talonarios')
                ->label('Tipo de Talonario a Asignar')
                ->options([
                    'preferenciales' => 'Preferenciales',
                    'regulares'      => 'Regulares',
                    'ambos'          => 'Preferenciales y Regulares',
                ])
                ->required()
                ->reactive()
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
                    ->label('Cajero Principal')
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

                Forms\Components\DatePicker::make('fecha_entrega')
                    ->label('Fecha de Entrega')
                    ->prefixIcon('heroicon-o-calendar')
                    ->default(now())
                    ->disabled()
                    ->dehydrated(true)
                    ->required(),
            ]),

                Forms\Components\Fieldset::make('Detalles del Talonario')
                    ->schema([
                        Forms\Components\TextInput::make('codigo_autorizacion')
                            ->label('Código de Autorización')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('tipo_talonario')
                            ->label('Tipo de Talonario')
                            ->options([
                                'preferencial' => 'Preferencial',
                                'regular' => 'Regular',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('cantidad_tickets')
                            ->label('Cantidad de Tickets')
                            ->numeric()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('numero_paquete')
                            ->label('Número de Paquete')
                            ->numeric()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('talonarios_por_paquete')
                            ->label('Cantidad de Talonarios en el Paquete')
                            ->numeric()
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('Rango y Valor')
                    ->schema([
                        Forms\Components\TextInput::make('rango_inicial')
                            ->label('Rango Inicial')
                            ->numeric()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('rango_final')
                            ->label('Rango Final')
                            ->numeric()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('valor_ticket_bs')
                            ->label('Valor de Ticket (Bs)')
                            ->numeric()
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(2),
            ]);
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
                    }),
                Tables\Columns\TextColumn::make('cantidad_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('rango_inicial_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_final_preferencial')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_restante_preferencial')
                    ->label('Preferenciales Restantes')
                    ->badge() // opcional: muestra como etiqueta de color
                    ->color(function ($state) {
                        if ($state < 800) {
                            return 'danger'; // rojo
                        } elseif ($state < 1200) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_preferencial')
                    ->label('Total a Recaudar Pref. ')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_regulares')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tipo_talonario')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'regular' => 'info',
                        'preferencial' => 'success',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('cajero.full_name')
                    ->label('Registrado por')
                    ->searchable(),
                    
                Tables\Columns\IconColumn::make('entregado_at')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('info')
                    ->getStateUsing(fn (InventarioTalonarios $record): bool => $record->entregado_at !== null)
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Entregado' : 'Disponible'),
                    
                Tables\Columns\TextColumn::make('cajeroActual.full_name')
                    ->label('Asignado a')
                    ->placeholder('No asignado')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('cantidad_tickets')
                    ->label('Tickets')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('rango_inicial')
                    ->label('Rango Inicial')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('rango_final')
                    ->label('Rango Final')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('valor_ticket_bs')
                    ->label('Valor (Bs)')
                    ->money('VES'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_talonario')
                    ->options([
                        'preferencial' => 'Preferencial',
                        'regular' => 'Regular',
                    ])
                    ->label('Tipo de Talonario'),
                    
                Tables\Filters\Filter::make('disponibilidad')
                    ->form([
                        Forms\Components\Select::make('estado')
                            ->options([
                                'disponible' => 'Disponible',
                                'entregado' => 'Entregado',
                            ])
                            ->label('Estado')
                            ->placeholder('Todos'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['estado'] === 'disponible', 
                            fn (Builder $query): Builder => $query->whereNull('entregado_at'),
                            fn (Builder $query): Builder => $data['estado'] === 'entregado' 
                                ? $query->whereNotNull('entregado_at')
                                : $query
                        );
                    }),
                    
                Tables\Filters\SelectFilter::make('cajero_actual_id')
                    ->relationship('cajeroActual', 'full_name')
                    ->label('Asignado a'),
            ])
            ->searchable()
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                //Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->can('delete', InventarioTalonarios::class))
                        ->action(function (Collection $records) {
                            // Solo eliminar los talonarios que no han sido entregados
                            $records->filter(fn ($record) => $record->entregado_at === null)->each->delete();
                        }),
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