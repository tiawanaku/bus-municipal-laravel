<?php

namespace App\Filament\Resources\AvisoResource\Pages;

use App\Filament\Resources\AvisoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAviso extends EditRecord
{
    protected static string $resource = AvisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
