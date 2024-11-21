<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntregaTalonario extends EditRecord
{
    protected static string $resource = EntregaTalonarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
