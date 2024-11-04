<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregaTalonarios extends ListRecords
{
    protected static string $resource = EntregaTalonarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
