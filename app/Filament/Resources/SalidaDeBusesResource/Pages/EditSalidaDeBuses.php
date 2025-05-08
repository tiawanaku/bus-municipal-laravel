<?php

namespace App\Filament\Resources\SalidaDeBusesResource\Pages;

use App\Filament\Resources\SalidaDeBusesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalidaDeBuses extends EditRecord
{
    protected static string $resource = SalidaDeBusesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    
}
