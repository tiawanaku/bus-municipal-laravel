<?php

namespace App\Filament\Resources\EntregaTalonariosAnfitrionResource\Pages;

use App\Filament\Resources\EntregaTalonariosAnfitrionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregaTalonariosAnfitrions extends ListRecords
{
    protected static string $resource = EntregaTalonariosAnfitrionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
