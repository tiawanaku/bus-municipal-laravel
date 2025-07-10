<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RendimientoTecnicosTable;
 use App\Filament\Widgets\SalidaBusesChart;
  use App\Filament\Widgets\TiposMantenimientosPastel;
 use Filament\Pages\Page;
 use App\Filament\Widgets\StatsOverviewWidget;
 use Illuminate\Support\Facades\Gate;

class MantenimientoDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static string $view = 'filament.pages.mantenimiento-dashboard';
    protected static ?string $title = 'Unidad de Mantenimiento';



   public $fechaInicio;
    public $fechaFin;
    public function updatedFechaInicio()
    {
        $this->dispatch('fechaActualizada', $this->fechaInicio, $this->fechaFin);
    }

    public function updatedFechaFin()
    {
        $this->dispatch('fechaActualizada', $this->fechaInicio, $this->fechaFin);
    }

// Permisos de visualizacion solo usuarios con permisos
    public static function canAccess(): bool
    {
        return Gate::allows('page_MantenimientoDashboard');
    }
}
