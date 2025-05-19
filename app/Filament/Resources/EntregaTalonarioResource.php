<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaTalonarioResource\Pages;
use App\Filament\Resources\EntregaTalonarioResource\RelationManagers;
use App\Models\EntregaTalonario;
use App\Models\InventarioTalonarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;

class EntregaTalonarioResource extends Resource
{
    protected static ?string $model = EntregaTalonario::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Entrega de Talonarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Datos de la Entrega')
                    ->schema([
                        Forms\Components\TextInput::make('responsable_entrega')
                            ->required()
                            ->label('Responsable de la Entrega')
                            ->columnSpan(1),

                        Forms\Components\Select::make('cajero_id')
                            ->relationship('cajero', 'full_name')
                            ->required()
                            ->searchable()
                            ->label('Cajero Receptor')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('fecha_entrega')
                            ->required()
                            ->default(now())
                            ->label('Fecha de Entrega')
                            ->columnSpan(1),

                        Forms\Components\Select::make('tipo_talonarios')
                            ->options([
                                'regular' => 'Regular',
                                'preferencial' => 'Preferencial',
                            ])
                            ->required()
                            ->label('Tipo de Talonarios')
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('inventario_talonarios_ids', []))
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Talonarios a Entregar')
                    ->schema([
                        Forms\Components\Select::make('inventario_talonarios_ids')
                            ->multiple()
                            ->label('Seleccionar Talonarios')
                            ->options(function (Get $get) {
                                $tipoTalonario = $get('tipo_talonarios');
                                
                                if (!$tipoTalonario) {
                                    return [];
                                }
                                
                                return InventarioTalonarios::query()
                                    ->where('tipo_talonario', $tipoTalonario)
                                    ->whereNull('entregado_at') // Solo talonarios no entregados
                                    ->get()
                                    ->mapWithKeys(function ($talonario) {
                                        return [
                                            $talonario->id => "Autorización: {$talonario->codigo_autorizacion} - " .
                                                "Rango: {$talonario->rango_inicial} a {$talonario->rango_final} - " .
                                                "Valor: {$talonario->valor_ticket_bs} Bs"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Seleccione los talonarios que desea entregar al cajero')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $talonariosIds = $get('inventario_talonarios_ids');
                                
                                if (empty($talonariosIds)) {
                                    $set('numero_paquetes_entregados', 0);
                                    $set('cantidad_talonarios', 0);
                                    $set('cantidad_tickets', 0);
                                    return;
                                }
                                
                                $talonarios = InventarioTalonarios::whereIn('id', $talonariosIds)->get();
                                
                                // Calcular totales
                                $numeroPaquetes = $talonarios->count();
                                $cantidadTalonarios = $talonarios->sum('talonarios_por_paquete');
                                $cantidadTickets = $talonarios->sum('cantidad_tickets');
                                
                                $set('numero_paquetes_entregados', $numeroPaquetes);
                                $set('cantidad_talonarios', $cantidadTalonarios);
                                $set('cantidad_tickets', $cantidadTickets);
                            }),

                        Forms\Components\Fieldset::make('Resumen de la Entrega')
                            ->schema([
                                Forms\Components\TextInput::make('numero_paquetes_entregados')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->label('Número de Paquetes'),

                                Forms\Components\TextInput::make('cantidad_talonarios')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->label('Cantidad de Talonarios'),

                                Forms\Components\TextInput::make('cantidad_tickets')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->label('Cantidad de Tickets'),
                            ])
                            ->columns(3),
                            
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('cajero.full_name')
                    ->label('Cajero')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('responsable_entrega')
                    ->label('Entregado por')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('tipo_talonarios')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'regular' => 'info',
                        'preferencial' => 'success',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('fecha_entrega')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('numero_paquetes_entregados')
                    ->label('Paquetes')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('cantidad_talonarios')
                    ->label('Talonarios')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('cantidad_tickets')
                    ->label('Tickets')
                    ->numeric(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_talonarios')
                    ->options([
                        'regular' => 'Regular',
                        'preferencial' => 'Preferencial',
                    ])
                    ->label('Tipo de Talonarios'),
                    
                Tables\Filters\SelectFilter::make('cajero_id')
                    ->relationship('cajero', 'full_name')
                    ->label('Cajero'),
                    
                Tables\Filters\Filter::make('fecha_entrega')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListEntregaTalonarios::route('/'),
            'create' => Pages\CreateEntregaTalonario::route('/create'),
            'edit' => Pages\EditEntregaTalonario::route('/{record}/edit'),
        ];
    }
}
