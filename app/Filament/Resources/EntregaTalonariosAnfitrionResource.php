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

                        Forms\Components\Select::make('recibido_por')  // Cajero Principal
                            ->label('Cajero secundario')
                            ->prefixIcon('heroicon-o-user')
                            ->options(function () {
                                return \App\Models\Cajero::where('tipo_cajero', 'secundario')  // Filtramos por cajeros principales
                                    ->get()
                                    ->mapWithKeys(function ($cajero) {
                                        $fullName = $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                                        return [$cajero->id => $fullName];
                                    });
                            })
                            ->searchable()  // Habilitar búsqueda
                            ->required(),  // Hacer que sea obligatorio


                        Forms\Components\Select::make('anfitrion_id')
                            ->label('Anfitrión')
                            ->relationship('anfitrion', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->nombre . ' ' . $record->apellido_paterno . ' ' . $record->apellido_materno)
                            ->required(),


                        Forms\Components\TextInput::make('numero_autorizacion')
                            ->label('Número de Autorización')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Preferenciales')->schema([
                    Forms\Components\TextInput::make('cantidad_talonarios_preferenciales')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_inicial_preferenciales')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_final_preferenciales')->numeric()->required(),
                ])->columns(3),

                Forms\Components\Section::make('Regulares')->schema([
                    Forms\Components\TextInput::make('cantidad_talonarios_regulares')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_inicial_regulares')->numeric()->required(),
                    Forms\Components\TextInput::make('rango_final_regulares')->numeric()->required(),

                ])->columns(3),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('anfitrion.nombre')->label('Anfitrión')->searchable(),
                Tables\Columns\TextColumn::make('recibido_por')->label('Recibido Por'), // Si quieres nombre relacionado, cambiar según relación
                Tables\Columns\TextColumn::make('numero_autorizacion')->label('Número Autorización')->searchable(),
                Tables\Columns\TextColumn::make('cantidad_talonarios_preferenciales')->label('Cant. Talonarios Preferenciales'),
                Tables\Columns\TextColumn::make('rango_inicial_preferenciales')->label('Rango Inicial Preferenciales'),
                Tables\Columns\TextColumn::make('rango_final_preferenciales')->label('Rango Final Preferenciales'),
                Tables\Columns\TextColumn::make('cantidad_talonarios_regulares')->label('Cant. Talonarios Regulares'),
                Tables\Columns\TextColumn::make('rango_inicial_regulares')->label('Rango Inicial Regulares'),
                Tables\Columns\TextColumn::make('rango_final_regulares')->label('Rango Final Regulares'),
                Tables\Columns\TextColumn::make('total_tickets_regulares')->label('Total Tickets Regulares'),
                Tables\Columns\TextColumn::make('total_tickets_preferenciales')->label('Total Tickets Preferenciales'),
                Tables\Columns\TextColumn::make('total_recaudar_regulares')->label('Total Recaudar Regulares')->money('BOB'),
                Tables\Columns\TextColumn::make('total_recaudar_preferenciales')->label('Total Recaudar Preferenciales')->money('BOB'),
                Tables\Columns\TextColumn::make('total_recaudar')->label('Total Recaudar')->money('BOB'),
                Tables\Columns\TextColumn::make('created_at')->label('Creado')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('updated_at')->label('Actualizado')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
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