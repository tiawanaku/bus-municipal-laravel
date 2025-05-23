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
            Forms\Components\Section::make('Datos de Entrega')
                ->columns(3)
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
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('anfitrion_id')
                        ->label('Anfitrión')
                        ->relationship('anfitrion', 'nombre')
                        ->getOptionLabelFromRecordUsing(fn($record) => $record->nombre . ' ' . $record->apellido_paterno . ' ' . $record->apellido_materno)
                        ->required(),

                    Forms\Components\TextInput::make('numero_autorizacion')
                        ->label('Número de Autorización')
                        ->required(),

                    Forms\Components\DatePicker::make('fecha_entrega')
                        ->label('Fecha de Entrega')
                        ->default(now())
                        ->required(),

                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3),
                ]),

            Forms\Components\Section::make('Preferenciales')->schema([
                Forms\Components\TextInput::make('cantidad_talonarios_preferenciales')
                    ->label('Cantidad de Talonarios Preferenciales')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('rango_inicial_preferenciales')
                    ->label('Rango Inicial Preferenciales')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('rango_final_preferenciales')
                    ->label('Rango Final Preferenciales')
                    ->numeric()
                    ->required(),
            ])->columns(3),

            Forms\Components\Section::make('Regulares')->schema([
                Forms\Components\TextInput::make('cantidad_talonarios_regulares')
                    ->label('Cantidad de Talonarios Regulares')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('rango_inicial_regulares')
                    ->label('Rango Inicial Regulares')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('rango_final_regulares')
                    ->label('Rango Final Regulares')
                    ->numeric()
                    ->required(),
            ])->columns(3),
        ]);
}


   public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('entrega_talonario_id')->label('Entrega Talonario ID'),
            Tables\Columns\TextColumn::make('anfitrion_id')->label('Anfitrión ID'),

            Tables\Columns\TextColumn::make('numero_autorizacion')->label('N° Autorización')->searchable(),

            Tables\Columns\TextColumn::make('cantidad_preferenciales')->label('Cant. Preferenciales'),
            Tables\Columns\TextColumn::make('rango_inicial_preferencial')->label('Rango Inicial Pref.'),
            Tables\Columns\TextColumn::make('rango_final_preferencial')->label('Rango Final Pref.'),
            Tables\Columns\TextColumn::make('total_boletos_preferenciales')->label('Total Boletos Pref.'),
            Tables\Columns\TextColumn::make('total_aproximado_bolivianos_preferencial')
                ->label('Total Bs. Pref.')
                ->money('BOB'),
            Tables\Columns\TextColumn::make('cantidad_restante_preferencial')->label('Cant. Restante Pref.'),

            Tables\Columns\TextColumn::make('cantidad_regulares')->label('Cant. Regulares'),
            Tables\Columns\TextColumn::make('rango_inicial_regular')->label('Rango Inicial Reg.'),
            Tables\Columns\TextColumn::make('rango_final_regular')->label('Rango Final Reg.'),
            Tables\Columns\TextColumn::make('total_boletos_regulares')->label('Total Boletos Reg.'),
            Tables\Columns\TextColumn::make('total_aproximado_bolivianos_regular')
                ->label('Total Bs. Reg.')
                ->money('BOB'),
            Tables\Columns\TextColumn::make('cantidad_restante_regular')->label('Cant. Restante Reg.'),

            Tables\Columns\TextColumn::make('estado_preferencial')->label('Estado Pref.'),
            Tables\Columns\TextColumn::make('estado_regular')->label('Estado Reg.'),
            Tables\Columns\TextColumn::make('tipo_talonarios')->label('Tipo Talonarios'),

            Tables\Columns\TextColumn::make('fecha_entrega')->label('Fecha de Entrega')->date('d/m/Y'),
            Tables\Columns\TextColumn::make('observaciones')->label('Observaciones')->limit(50),

            Tables\Columns\TextColumn::make('total_recaudacion_bolivianos')
                ->label('Total Recaudación Bs.')
                ->money('BOB'),

            Tables\Columns\TextColumn::make('created_at')->label('Creado')->dateTime('d/m/Y H:i'),
            Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')->dateTime('d/m/Y H:i'),
        ])
        ->filters([
            // Puedes agregar filtros aquí si deseas
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
            'index' => Pages\ListEntregaTalonariosAnfitrions::route('/'),
            'create' => Pages\CreateEntregaTalonariosAnfitrion::route('/create'),
            'edit' => Pages\EditEntregaTalonariosAnfitrion::route('/{record}/edit'),
        ];
    }
}