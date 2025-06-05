<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EntregaTalonarioResource\Widgets\Cajeros;

class ListEntregaTalonarios extends ListRecords
{
    protected static string $resource = EntregaTalonarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            Cajeros::class,
        ];
    }
}
