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


class EntregaTalonarioResource extends Resource
{
    protected static ?string $model = EntregaTalonario::class;

    protected static ?string $navigationGroup = 'Gestión de Talonarios';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cajero_id')
                    ->label('Cajero')
                    ->options(function () {
                        return \App\Models\Cajero::all()->pluck('nombre', 'id')->mapWithKeys(function ($item, $key) {
                            $cajero = \App\Models\Cajero::find($key);
                            $fullName = $cajero->nombre . ' ' . $cajero->apellido_paterno . ' ' . $cajero->apellido_materno;
                            return [$key => $fullName];
                        });
                    })
                    ->required(),


                Forms\Components\Select::make('users_id')
                    ->label('Anfitrión')
                    ->options(function () {
                        return \App\Models\User::all()->pluck('name', 'id')->mapWithKeys(function ($item, $key) {
                            $user = \App\Models\User::find($key);
                            $fullName = $user->name . ' ' . $user->apellido_paterno . ' ' . $user->apellido_materno;
                            return [$key => $fullName];
                        });
                    })
                    ->nullable(),


                Forms\Components\TextInput::make('cantidad_preferenciales')->nullable(),
                Forms\Components\TextInput::make('rango_inicial_preferencial')->nullable(),
                Forms\Components\TextInput::make('rango_final_preferencial')->nullable(),
                Forms\Components\TextInput::make('cantidad_restante_preferencial')->nullable(),
                Forms\Components\TextInput::make('cantidad_regulares')->nullable(),
                Forms\Components\TextInput::make('rango_inicial_regular')->nullable(),
                Forms\Components\TextInput::make('rango_final_regular')->nullable(),
                Forms\Components\TextInput::make('cantidad_restante_regular')->nullable(),
                Forms\Components\DatePicker::make('fecha_entrega')->required(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('responsable_entrega'),
                Tables\Columns\TextColumn::make('cantidad_preferenciales'),
                Tables\Columns\TextColumn::make('rango_inicial_preferencial'),
                Tables\Columns\TextColumn::make('rango_final_preferencial'),
                Tables\Columns\TextColumn::make('cantidad_restante_preferencial'),
                Tables\Columns\TextColumn::make('cantidad_regulares'),
                Tables\Columns\TextColumn::make('rango_inicial_regular'),
                Tables\Columns\TextColumn::make('rango_final_regular'),
                Tables\Columns\TextColumn::make('cantidad_restante_regular'),
                Tables\Columns\TextColumn::make('fecha_entrega'),
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
            'index' => Pages\ListEntregaTalonarios::route('/'),
            'create' => Pages\CreateEntregaTalonario::route('/create'),
            'edit' => Pages\EditEntregaTalonario::route('/{record}/edit'),
        ];
    }
}