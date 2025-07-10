<?php

namespace App\Filament\Pages;
use Illuminate\Support\Facades\Gate;

use Filament\Pages\Page;

class OperacionesDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.operaciones-dashboard';
    protected static ?string $title = 'Unidad de Operaciones';


    // Permisos de visualizacion solo usuarios con permisos
    public static function canAccess(): bool
    {
        return Gate::allows('page_OperacionesDashboard');
    }
}
