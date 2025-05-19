<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;
use App\Filament\Resources\EntregaTalonariosAnfitrionResource\RelationManagers;
use App\Models\EntregaTalonariosAnfitrion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntregaTalonariosAnfitrionResource extends Resource
{
    protected static ?string $model = EntregaTalonariosAnfitrion::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestión de Talonarios';

   public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Pregunta al usuario al principio del formulario
            Forms\Components\Select::make('tipo_talonario')
                ->label('¿Qué tipo de talonario desea registrar?')
                ->options([
                    'preferencial' => 'Preferenciales',
                    'regular' => 'Regulares',
                    'ambos' => 'Ambos',
                ])
                ->reactive()
                ->required()
                ->columnSpanFull(),

            Forms\Components\Section::make('Datos de Entrega')
                ->columns(4)
                ->schema([

                    Forms\Components\Select::make('entrega_talonario_id')  // Este campo luego se asigna a cajero_id
                        ->label('Cajero secundario')
                        ->prefixIcon('heroicon-o-user')
                        ->options(function () {
                            return \App\Models\Cajero::where('tipo_cajero', 'secundario')
                                ->get()
                                ->mapWithKeys(function ($cajero) {
                                    $fullName = $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                                    return [$cajero->id => $fullName];
                                });
                        })
                        ->required(),

                    Forms\Components\Select::make('anfitrion_id')
                        ->label('Anfitrión')
                        ->prefixIcon('heroicon-o-user')
                        ->relationship('anfitrion', 'nombre')
                        ->getOptionLabelFromRecordUsing(fn($record) => $record->nombre . ' ' . $record->apellido_paterno . ' ' . $record->apellido_materno)
                        ->required(),

                    Forms\Components\TextInput::make('numero_autorizacion')
                        ->prefixIcon('heroicon-o-key')
                        ->label('Número de Autorización')
                        ->required(),

                   Forms\Components\DatePicker::make('fecha_entrega')
                    ->label('Fecha de Entrega')
                    ->prefixIcon('heroicon-o-calendar')
                    ->default(now())
                    ->disabled()  // Deshabilita el campo para que no se pueda cambiar
                    ->required(),

                ]),

            Forms\Components\Section::make('Preferenciales')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('cantidad_talonarios_preferenciales')
                        ->label('Cantidad de Talonarios Preferenciales')
                        ->prefixIcon('heroicon-o-hashtag')
                        ->numeric(),

                    Forms\Components\TextInput::make('rango_inicial_preferenciales')
                        ->label('Rango Inicial Preferenciales')
                        ->prefixIcon('heroicon-o-arrow-down')
                        ->numeric(),
                ])
                ->visible(fn (Forms\Get $get) => in_array($get('tipo_talonario'), ['preferencial', 'ambos'])),

            Forms\Components\Section::make('Regulares')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('cantidad_talonarios_regulares')
                        ->label('Cantidad de Talonarios Regulares')
                        ->prefixIcon('heroicon-o-hashtag')
                        ->numeric(),

                    Forms\Components\TextInput::make('rango_inicial_regulares')
                        ->label('Rango Inicial Regulares')
                        ->prefixIcon('heroicon-o-arrow-down')
                        ->numeric(),
                ])
                ->visible(fn (Forms\Get $get) => in_array($get('tipo_talonario'), ['regular', 'ambos'])),

            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3),
                ]),
        ]);
}


   public static function table(Table $table): Table
{
    return $table
        ->columns([

            Tables\Columns\TextColumn::make('entrega_talonario_id')
               ->label('Cajer@s')
                        ->toggleable(isToggledHiddenByDefault: true)
                         ->getStateUsing(function ($record) {
                                $cajero = \App\Models\Cajero::find($record->entrega_talonario_id);
                                return $cajero ? $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno : 'No disponible';
                  }),


           Tables\Columns\TextColumn::make('anfitrion_id')
                    ->label('Anfitrión')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                           $anfitrion = \App\Models\Anfitrion::find($record->anfitrion_id);  // Cambia el modelo si se llama distinto
                      return $anfitrion ? $anfitrion->nombre . ' ' . $anfitrion->apellido_paterno . ' ' . $anfitrion->apellido_materno : 'No disponible';
                  }),   

            Tables\Columns\TextColumn::make('numero_autorizacion')
            ->label('N° Autorización')
            ->toggleable(isToggledHiddenByDefault: true)
            ->searchable(),

            Tables\Columns\TextColumn::make('cantidad_preferenciales')
            ->toggleable(isToggledHiddenByDefault: true)
            ->label('Cant. Preferencial'),

            Tables\Columns\TextColumn::make('rango_inicial_preferencial')
            ->toggleable(isToggledHiddenByDefault: true)
            ->label('Rango Inicial Pref.'),

            Tables\Columns\TextColumn::make('rango_final_preferencial')
            ->toggleable(isToggledHiddenByDefault: true)
            ->label('Rango Final Pref.'),

            Tables\Columns\TextColumn::make('total_boletos_preferenciales')
                ->label('Total Tickets Pref.')
                    ->color(function ($state) {
                         if ($state < 800) {
                             return 'danger';    // rojo
                             } elseif ($state >= 800 && $state <= 1500) {
                                return 'warning';  // amarillo
                            } else {
                               return 'success';  // verde
                        }   
                         }),


            Tables\Columns\TextColumn::make('total_aproximado_bolivianos_preferencial')
                 ->label('Recaudo Preferecial Bs.')
                 ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                 ->color('warning'),

            Tables\Columns\TextColumn::make('cantidad_restante_preferencial')
            ->toggleable(isToggledHiddenByDefault: true)
            ->label('Cant. Restante Pref.'),

            Tables\Columns\TextColumn::make('cantidad_regulares')
            ->toggleable(isToggledHiddenByDefault: true)
            ->label('Cant. Regulares'),

            Tables\Columns\TextColumn::make('rango_inicial_regular')
            ->toggleable(isToggledHiddenByDefault: true)
            ->label('Rango Inicial Reg.'),

            Tables\Columns\TextColumn::make('rango_final_regular')
            ->toggleable(isToggledHiddenByDefault: true)
            ->label('Rango Final Reg.'),

           Tables\Columns\TextColumn::make('total_boletos_regulares')
                ->label('Total Boletos Reg.')
                ->color(function ($state) {
                     if ($state < 800) {
                         return 'danger';    // rojo
                         } elseif ($state >= 800 && $state <= 1500) {
                                 return 'warning';  // amarillo
                            } else {
                                  return 'success';  // verde
                        }
                }),


            Tables\Columns\TextColumn::make('total_aproximado_bolivianos_regular')
                 ->label('Recaudo Regular Bs.')
                 ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                 ->color('warning'),

            Tables\Columns\TextColumn::make('cantidad_restante_regular')
                    ->label('Cant. Restante Reg.')
                    ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('tipo_talonarios')
             ->toggleable(isToggledHiddenByDefault: true)
            ->label('Tipo Talonarios'),

          

            Tables\Columns\TextColumn::make('observaciones')
             ->toggleable(isToggledHiddenByDefault: true)
            ->label('Observaciones')->limit(50),

            Tables\Columns\TextColumn::make('total_recaudacion_bolivianos')
                ->label('Total Recaudación Bs.')
                ->formatStateUsing(fn($state) => 'Bs. ' . number_format($state, 2, '.', ','))
                ->color('warning'),

            Tables\Columns\TextColumn::make('fecha_entrega')
            ->label('Fecha de Entrega')
            ->toggleable(isToggledHiddenByDefault: true)
            ->searchable()
            ->date('d/m/Y'),

            Tables\Columns\TextColumn::make('created_at')->label('Creado')
            ->toggleable(isToggledHiddenByDefault: true)
            ->dateTime('d/m/Y H:i'),

            Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')
            ->toggleable(isToggledHiddenByDefault: true)
            ->dateTime('d/m/Y H:i'),
        ])
        ->filters([
            // Puedes agregar filtros aquí si deseas
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
            'index' => Pages\ListEntregaTalonariosAnfitrions::route('/'),
            'create' => Pages\CreateEntregaTalonariosAnfitrion::route('/create'),
            'edit' => Pages\EditEntregaTalonariosAnfitrion::route('/{record}/edit'),
        ];
    }
}