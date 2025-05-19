<?php

namespace App\Filament\Resources\FormularioRecaudoResource\Pages;

use App\Filament\Resources\FormularioRecaudoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormularioRecaudos extends ListRecords
{
    protected static string $resource = FormularioRecaudoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
