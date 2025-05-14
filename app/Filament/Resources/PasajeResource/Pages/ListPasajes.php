<?php

namespace App\Filament\Resources\PasajeResource\Pages;

use App\Filament\Resources\PasajeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPasajes extends ListRecords
{
    protected static string $resource = PasajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
