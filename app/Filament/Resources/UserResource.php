<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    //para cambiar el nombre de la etiqueta
    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Administrador del Sistema';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Tarjeta para información personal
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->hint('Introduce el nombre')
                                    ->prefixIcon('heroicon-o-user'),

                                Forms\Components\TextInput::make('apellido_paterno')
                                    ->label('Apellido Paterno')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-user'),

                                Forms\Components\TextInput::make('apellido_materno')
                                    ->label('Apellido Materno')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-user'),

                                Forms\Components\TextInput::make('ci')
                                    ->label('CI')
                                    ->required()
                                    ->maxLength(20)
                                    ->prefixIcon('heroicon-o-identification'),

                                Forms\Components\TextInput::make('complemento_ci')
                                    ->label('Complemento CI')
                                    ->nullable()
                                    ->maxLength(10)
                                    ->prefixIcon('heroicon-o-plus'),

                                Forms\Components\TextInput::make('celular')
                                    ->label('Celular')
                                    ->nullable()
                                    ->maxLength(20)
                                    ->prefixIcon('heroicon-o-phone'),
                            ]),
                    ])
                    ->label('Información Personal'),

                // Tarjeta para contacto
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-envelope'),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->required()
                            ->password()
                            ->minLength(8)
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-shield-check'),
                    ])
                    ->label('Datos de Contacto'),

                // Tarjeta para asignación de roles
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Roles')
                            ->prefixIcon('heroicon-o-shield-check'),
                    ])
                    ->label('Roles'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre Completo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('apellido_paterno')
                    ->label('Apellido Paterno')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('apellido_materno')
                    ->label('Apellido Materno')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Validar')
                    ->icon('heroicon-m-check-badge')
                    ->action(function (User $user) {
                        $user->email_verified_at = Date('Y-m-d H:i:s');
                        $user->save();
                        Notification::make()
                            ->title('Acción realizada')
                            ->body('El correo del usuario ha sido marcado como válido.')
                            ->success() // Puedes usar ->success(), ->warning(), etc., según el tipo de mensaje
                            ->send();
                    }),
                Tables\Actions\Action::make('Rechazar')
                    ->icon('heroicon-m-x-circle')
                    ->action(function (User $user) {
                        $user->email_verified_at = null;
                        $user->save();
                        Notification::make()
                            ->title('Acción realizada')
                            ->body('El correo del usuario ha sido marcado como inválido.')
                            ->danger() // Puedes usar ->success(), ->warning(), etc., según el tipo de mensaje
                            ->send();
                    })
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}