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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')
                ->required(),
                TextInput::make('email')
                ->required()
                ->email(),
                TextInput::make('password')
                ->password()
                ->required(),
                Select::make('roles')->multiple()->relationship('roles','name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('email_verified_at'),
                TextColumn::make('roles.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Validar')
                ->icon('heroicon-m-check-badge')
                ->action(function (User $user){
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
                ->action(function (User $user){
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
