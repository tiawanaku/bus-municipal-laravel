<?php

namespace App\Filament\Resources\ContenidoResource\Pages;

use App\Filament\Resources\ContenidoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContenido extends EditRecord
{
    protected static string $resource = ContenidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
