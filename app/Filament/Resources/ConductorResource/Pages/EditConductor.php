<?php

namespace App\Filament\Resources\ConductorResource\Pages;

use App\Filament\Resources\ConductorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConductor extends EditRecord
{
    protected static string $resource = ConductorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
