<?php

namespace App\Filament\Resources\InventarioTalonariosResource\Pages;

use App\Filament\Resources\InventarioTalonariosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\InventarioStatsWidget;

class ListInventarioTalonarios extends ListRecords
{
    protected static string $resource = InventarioTalonariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\InventarioTalonariosResource\Widgets\InventarioStatsWidget::class,
        ];
    }
}