<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaTalonarioResource\Pages;
use App\Filament\Resources\EntregaTalonarioResource\RelationManagers;
use App\Models\EntregaTalonario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;



use Filament\Notifications\Notification;

use Filament\Forms\Components\Section;

use Filament\Forms\Components\Placeholder;
use App\Models\InventarioTalonario;
use Illuminate\Validation\ValidationException;

class EntregaTalonarioResource extends Resource
{
    protected static ?string $model = EntregaTalonario::class;

    protected static ?string $navigationGroup = 'Gestión de Talonarios';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Datos Principales')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('cajero_id')
                            ->label('Cajero ID')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('inventario_id')
                            ->label('Inventario ID')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-clipboard-document-list'),

                             
                        Forms\Components\DatePicker::make('fecha_entrega')
                            ->label('Fecha Entrega')
                            ->required()
                            ->prefixIcon('heroicon-o-calendar'),
                    ]),
                ]),

            Forms\Components\Section::make('Preferenciales')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('cantidad_preferenciales')
                            ->label('Cantidad Preferenciales')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-hashtag'),

                        Forms\Components\TextInput::make('rango_inicial_preferencial')
                            ->label('Rango Inicial Preferencial')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-arrow-down'),
                            
                    ]),
                ]),

            Forms\Components\Section::make('Regulares')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('cantidad_regulares')
                            ->label('Cantidad Regulares')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-hashtag'),

                        Forms\Components\TextInput::make('rango_inicial_regular')
                            ->label('Rango Inicial Regular')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-arrow-down'),
                    ]),
                ]),

            Forms\Components\Section::make('Entrega y Observaciones')
                ->schema([
                    Forms\Components\Grid::make(1)->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->maxLength(255)
                            ->nullable(),
                    ]),
                ]),
        ]);
}


    public static function saving(EntregaTalonario $record)
    {
        $inicioPref = request('rango_inicial_preferencial');
        $finPref = request('rango_final_preferencial');

        $inicioReg = request('rango_inicial_regular');
        $finReg = request('rango_final_regular');

        // Validar Preferenciales
        $existePref = \App\Models\EntregaTalonario::where(function ($query) use ($inicioPref, $finPref) {
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

            throw new \Exception('Rango preferencial inválido.');
        }

        // Validar Regulares
        $existeReg = \App\Models\EntregaTalonario::where(function ($query) use ($inicioReg, $finReg) {
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

            throw new \Exception('Rango regular inválido.');
        }
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('users_id')
                    ->label('Responsable de Entrega')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        $user = \App\Models\User::find($record->users_id);
                        return $user ? $user->name . ' ' . $user->apellido_paterno . ' ' . $user->apellido_materno : 'No disponible';
                    }),

                Tables\Columns\TextColumn::make('cajero_id')
                    ->label('Cajero')
                    ->searchable()
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
                    ->badge()
                    ->color(function ($state) {
                        if ($state < 400) {
                            return 'danger'; // rojo
                        } elseif ($state < 800) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_preferenciales')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('estado_preferencial')
                    ->label('Estado Preferencial')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            0 => 'asignado',
                            1 => 'asignable',
                            2 => 'no asignable',
                            default => 'desconocido',
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            0 => 'danger',       // asignado → verde
                            1 => 'success',       // asignable → azul
                            2 => 'primary',         // no asignable → rojo
                            default => 'gray',    // desconocido
                        };
                    }),



                Tables\Columns\TextColumn::make('cantidad_regulares')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('rango_inicial_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rango_final_regular')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cantidad_restante_regular')
                    ->label('Regulares Restantes')
                    ->badge()
                    ->color(function ($state) {
                        if ($state < 400) {
                            return 'danger'; // rojo
                        } elseif ($state < 800) {
                            return 'warning'; // amarillo
                        } else {
                            return 'success'; // verde
                        }
                    }),

                Tables\Columns\TextColumn::make('total_boletos_regulares')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_aproximado_bolivianos_regular')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                    ->color('warning') // Color amaril
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('estado_regular')
                    ->label('Estado Preferencial')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            0 => 'asignado',
                            1 => 'asignable',
                            2 => 'no asignable',
                            default => 'desconocido',
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            0 => 'danger',       // asignado → verde
                            1 => 'success',       // asignable → azul
                            2 => 'primary',        // no asignable → rojo
                            default => 'gray',    // desconocido
                        };
                    }),


                Tables\Columns\TextColumn::make('fecha_entrega')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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