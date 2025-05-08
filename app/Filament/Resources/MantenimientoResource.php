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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

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
                        // Obteniendo el bus seleccionado y su kilometraje anterior
                        $bus = Bus::find($state);
                        if ($bus) {

                            $set('km_anterior', $bus->mantenimientos()->latest()->first()?->km_actual);
                        }
                    }),
                Forms\Components\DatePicker::make('fecha_mantenimiento')
                    ->required(),
                Forms\Components\TextInput::make('km_actual')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->lazy()
                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                        // Solo actualizar si el campo no es null
                        $anterior = $get('km_anterior');
                        $actual = $state;
        
                        if (!is_null($actual) && is_numeric($anterior) && is_numeric($actual)) {
                            $set('km_actual_recorrido', $actual - $anterior);
                            $tipoMantenimiento = Mantenimiento::calcularTipoPorKm($actual);
                            $set('tipo_mantenimiento', $tipoMantenimiento);
                        }
                    })
                    ->label('Kilometraje Actual')
                    ->rules(function (callable $get) {
                        $anterior = $get('km_anterior');
                        return [
                            function ($attribute, $value, $fail) use ($anterior) {
                                if (is_numeric($value) && is_numeric($anterior) && $value < $anterior) {
                                    $fail('El kilometraje actual no puede ser menor al anterior (' . $anterior . ').');
                                }
                            },
                        ];
                    }),
                Forms\Components\TextInput::make('km_actual_recorrido')
                    ->required()
                    ->readonly(),

                Forms\Components\TextInput::make('tipo_mantenimiento')
                    ->label('Tipo de Mantenimiento')
                    ->required()
                    ->readonly(),
                TextInput::make('km_anterior')
                    ->numeric()
                    ->readonly()
                    ->label('Kilometraje Anterior')
                    ->default(0)
                    ->dehydrateStateUsing(fn($state) => $state ?: 0)
                ,


                Forms\Components\Textarea::make('observaciones'),
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
                Tables\Columns\TextColumn::make('observaciones'),
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
