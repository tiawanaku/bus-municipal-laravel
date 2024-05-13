<?php

namespace App\Filament\Resources\CajeroResource\Pages;

use App\Filament\Resources\CajeroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCajero extends EditRecord
{
    protected static string $resource = CajeroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
