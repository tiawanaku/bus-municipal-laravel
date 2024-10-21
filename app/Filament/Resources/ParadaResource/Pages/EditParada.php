<?php

namespace App\Filament\Resources\ParadaResource\Pages;

use App\Filament\Resources\ParadaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParada extends EditRecord
{
    protected static string $resource = ParadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
