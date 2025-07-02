<?php

namespace App\Filament\Resources\ControlDeReguladorResource\Pages;

use App\Filament\Resources\ControlDeReguladorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditControlDeRegulador extends EditRecord
{
    protected static string $resource = ControlDeReguladorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
