<?php

namespace App\Filament\Resources\AsignacionDeBusResource\Pages;

use App\Filament\Resources\AsignacionDeBusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsignacionDeBuses extends ListRecords
{
    protected static string $resource = AsignacionDeBusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
