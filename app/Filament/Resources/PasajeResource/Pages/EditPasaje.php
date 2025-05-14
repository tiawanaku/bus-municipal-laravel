<?php

namespace App\Filament\Resources\PasajeResource\Pages;

use App\Filament\Resources\PasajeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPasaje extends EditRecord
{
    protected static string $resource = PasajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
