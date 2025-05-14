<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Pages;

use App\Filament\Resources\InventarioTalonariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables;

class EditInventarioTalonarios extends EditRecord
{
    protected static string $resource = InventarioTalonariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

        ];
    }
}
