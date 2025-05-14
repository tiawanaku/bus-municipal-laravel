<?php

namespace App\Filament\Resources\GaleriaResource\Pages;

use App\Filament\Resources\GaleriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGaleria extends EditRecord
{
    protected static string $resource = GaleriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
