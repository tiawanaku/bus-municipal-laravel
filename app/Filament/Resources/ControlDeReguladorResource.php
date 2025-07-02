<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlDeReguladorResource\Pages;
use App\Filament\Resources\ControlDeReguladorResource\RelationManagers;
use App\Models\ControlDeRegulador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ControlDeReguladorResource extends Resource
{
    protected static ?string $model = ControlDeRegulador::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('molinete_inicial')
                ->required()
                ->numeric()
                ->reactive(),

            Forms\Components\FileUpload::make('foto_respaldo_inicial')
                ->required(),

            Forms\Components\TextInput::make('molinete_final')
                ->required()
                ->numeric()
                ->reactive()
                ->afterStateUpdated(function (callable $set, callable $get) {
                    $inicio = (int) $get('molinete_inicial');
                    $fin = (int) $get('molinete_final');
                    $confirmado = $get('confirmar_datos');

                    if ($confirmado && $fin >= $inicio) {
                        $set('total_giros', $fin - $inicio);
                    }
                }),

            Forms\Components\FileUpload::make('foto_respaldo_final')
                ->required(),

            Forms\Components\TextInput::make('total_giros')
                ->label('Cantidad de giros realizados por el molinete')
                ->required()
                ->numeric()
                ->readonly(),

           Forms\Components\TextInput::make('user.name')
    ->label('Usuario')
    ->default(fn () => auth()->user()->name)
    ->disabled(),

Forms\Components\Hidden::make('user_id')
    ->default(fn () => auth()->id()),

            Forms\Components\Select::make('bus_id')
                ->label('Bus')
                ->relationship('bus', 'numero_bus')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\DateTimePicker::make('fecha_envio')
                ->label('Fecha y hora de envío')
                ->nullable(),

            Forms\Components\Checkbox::make('confirmar_datos')
                ->label('Confirmo que los datos son correctos')
                ->reactive()
                ->afterStateUpdated(function (callable $set, callable $get) {
                    $inicio = (int) $get('molinete_inicial');
                    $fin = (int) $get('molinete_final');

                    if ($get('confirmar_datos') && $fin >= $inicio) {
                        $set('total_giros', $fin - $inicio);
                    }
                }),
        ]);
}

    public static function table(Table $table): Table
    {
       return $table
            ->columns([
    Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
    Tables\Columns\TextColumn::make('molinete_inicial')->label('Molinete Inicial')->sortable(),
    Tables\Columns\TextColumn::make('foto_respaldo_inicial')
        ->label('Foto Respaldo Inicial')
        ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
    Tables\Columns\TextColumn::make('molinete_final')->label('Molinete Final')->sortable(),
    Tables\Columns\TextColumn::make('foto_respaldo_final')
        ->label('Foto Respaldo Final')
        ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
    Tables\Columns\TextColumn::make('total_giros')->label('Total Giros')->sortable(),
    Tables\Columns\TextColumn::make('fecha_envio')
        ->label('Fecha Envío')
        ->dateTime()
        ->sortable(),
    Tables\Columns\TextColumn::make('user.name')->label('Usuario')->sortable(),
    Tables\Columns\TextColumn::make('bus.numero_bus')->label('Bus')->sortable(),
    Tables\Columns\TextColumn::make('created_at')
        ->label('Creado')
        ->dateTime()
        ->sortable(),
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
            'index' => Pages\ListControlDeReguladors::route('/'),
            'create' => Pages\CreateControlDeRegulador::route('/create'),
            'edit' => Pages\EditControlDeRegulador::route('/{record}/edit'),
        ];
    }
}
