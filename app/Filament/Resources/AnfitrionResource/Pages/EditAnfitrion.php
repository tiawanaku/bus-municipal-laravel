<?php

namespace App\Filament\Resources\AnfitrionResource\Pages;

use App\Filament\Resources\AnfitrionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnfitrion extends EditRecord
{
    protected static string $resource = AnfitrionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
