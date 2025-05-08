<?php

namespace App\Filament\Resources\MantenimientoResource\Pages;

use App\Filament\Resources\MantenimientoResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;

class ListMantenimientos extends ListRecords
{
    protected static string $resource = MantenimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('createPDF')
                ->label('Crear PDF')
                ->color('warning')
                ->requiresConfirmation()
                ->url(
                    fn (): string => route('pdf.example'),
                    shouldOpenInNewTab: true
                ),
        ];
    }
}
