<?php

namespace App\Filament\Resources\AsignacionDeBusResource\Pages;

use App\Filament\Resources\AsignacionDeBusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsignacionDeBus extends EditRecord
{
    protected static string $resource = AsignacionDeBusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
