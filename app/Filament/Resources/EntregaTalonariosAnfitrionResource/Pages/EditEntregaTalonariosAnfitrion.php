<?php

namespace App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntregaTalonariosAnfitrion extends EditRecord
{
    protected static string $resource = EntregaTalonariosAnfitrionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
