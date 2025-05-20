<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusResource\Pages;
use App\Filament\Resources\BusResource\RelationManagers;
use App\Models\Bus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Support\Facades\Http;
use Filament\Notifications\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Button;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;




class BusResource extends Resource
{
    protected static ?string $model = Bus::class;

    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                //  Información del Bus
                Card::make('Datos del Bus')
                    ->schema([
                        TextInput::make('numero_placa')
                            ->label('Número de Placa')
                            ->prefixIcon('heroicon-o-truck')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('numero_bus')
                            ->label('Número de Bus')
                            ->prefixIcon('heroicon-o-truck')
                            ->required()
                            ->maxLength(255),
                    ]),

                Card::make('Datos del GPS')->schema([

                    Toggle::make('nuevoGPS')
                        ->label('Registrar nuevo GPS')
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state) {
                                //  Guardamos temporalmente los datos del GPS actual
                                $set('backup_uniqueId', $get('uniqueId'));
                                $set('backup_name', $get('name'));
                                $set('backup_model', $get('model'));
                                $set('backup_category', $get('category'));
                                $set('backup_phone', $get('phone'));

                                // Limpiamos los campos para registrar nuevo GPS
                                $set('uniqueId', '');
                                $set('name', '');
                                $set('model', '');
                                $set('category', '');
                                $set('phone', '');
                            } else {
                                // Restauramos los datos guardados si el usuario desactiva
                                $set('uniqueId', $get('backup_uniqueId'));
                                $set('name', $get('backup_name'));
                                $set('model', $get('backup_model'));
                                $set('category', $get('backup_category'));
                                $set('phone', $get('backup_phone'));
                            }
                        }),

                    Select::make('uniqueId')
                        ->label('ID único GPS')
                        ->options(fn() => BusResource::getAvailableDevices())
                        ->live()
                        ->required()
                        ->unique('buses', 'uniqueId')
                        ->validationMessages([
                            'unique' => 'Este ID único GPS ya está registrado en otro bus.',
                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (!empty($state)) {
                                $device = BusResource::fetchDeviceData($state);
                                $set('name', $device['name'] ?? '');
                                $set('model', $device['model'] ?? '');
                                $set('category', $device['category'] ?? '');
                                $set('phone', $device['phone'] ?? '');
                            }
                        })
                        ->hidden(fn($get) => $get('nuevoGPS') === true),

                    TextInput::make('uniqueIdManual')
                        ->label('ID único GPS (Manual)')
                        ->required()
                        ->unique('buses', 'uniqueId')
                        ->validationMessages([
                            'unique' => 'Este ID único GPS ya está registrado en otro bus.',
                        ])
                        ->hidden(fn($get) => $get('nuevoGPS') === false), // Oculta cuando el toggle está desactivado

                    TextInput::make('name')
                        ->label('Nombre del dispositivo'),

                    TextInput::make('model')
                        ->label('Modelo'),

                    TextInput::make('phone')
                        ->label('Número de teléfono'),

                    TextInput::make('category')
                        ->label('Categoría')
                        ->default('bus'),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('numero_placa')
                    ->searchable()
                    ->size('lg'),
                Tables\Columns\TextColumn::make('numero_bus')
                    ->sortable(),
                Tables\Columns\TextColumn::make('uniqueId')
                    ->searchable()
                    ->size('lg'),


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
            'index' => Pages\ListBuses::route('/'),
            'create' => Pages\CreateBus::route('/create'),
            'edit' => Pages\EditBus::route('/{record}/edit'),
        ];
    }

    /* Para enviar a TRACCAR */

    public static function getAvailableDevices(): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode('milenkaelisaq95@gmail.com:71256293') // Reemplaza con credenciales válidas
        ])->withoutVerifying()->get("https://demo.traccar.org/api/devices");

        if ($response->failed()) {
            return [];
        }

        return collect($response->json())->mapWithKeys(fn($device) => [
            $device['uniqueId'] => "{$device['name']} ({$device['uniqueId']})"
        ])->toArray();
    }
    public static function fetchDeviceData($uniqueId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode('milenkaelisaq95@gmail.com:71256293') 
        ])->withoutVerifying()->get("https://demo.traccar.org/api/devices?uniqueId={$uniqueId}");

        return $response->successful() ? collect($response->json())->first() ?? [] : [];
    }
}
