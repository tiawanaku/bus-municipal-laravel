<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class RecaudoDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.recaudo-dashboard';
    protected static ?string $title = 'Unidad de Recaudo';

    // Permisos de visualizacion solo usuarios con permisos
    public static function canAccess(): bool
    {
        return Gate::allows('page_RecaudoDashboard');
    }
}
