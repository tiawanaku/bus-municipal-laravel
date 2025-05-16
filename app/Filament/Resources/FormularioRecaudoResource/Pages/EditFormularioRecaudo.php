<?php

namespace App\Filament\Resources\FormularioRecaudoResource\Pages;

use App\Filament\Resources\FormularioRecaudoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormularioRecaudo extends EditRecord
{
    protected static string $resource = FormularioRecaudoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
