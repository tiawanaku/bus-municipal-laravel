<?php

namespace App\Filament\Resources\ParadaResource\Pages;

use App\Filament\Resources\ParadaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParadas extends ListRecords
{
    protected static string $resource = ParadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
