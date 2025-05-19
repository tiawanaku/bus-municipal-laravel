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
                Forms\Components\Fieldset::make('Información del Responsable')
                    ->schema([
                        Forms\Components\Select::make('cajero_id')
                            ->label('Cajero Responsable')
                            ->options(function () {
                                return \App\Models\Cajero::all()
                                    ->pluck('full_name', 'id');
                            })
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

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
                Tables\Columns\TextColumn::make('codigo_autorizacion')
                    ->label('N° Autorización')
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
                Tables\Actions\EditAction::make()->visible(fn (InventarioTalonarios $record): bool => $record->entregado_at === null),
                Tables\Actions\ViewAction::make(),
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