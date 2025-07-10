<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Support\Facades\Gate;
class CentroMonitoreo extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $title = 'Centro de Monitoreo de Buses';

    protected static string $view = 'filament.pages.centro-monitoreo';


    public $bus_id;
    public $fecha_inicio;
    public $fecha_fin;
    public $iframePDF;

    protected function getFormSchema(): array
    {

        $options =  \App\Models\Bus::all()->pluck('numero_placa', 'id')->toArray();

        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Generar reporte de recorrido de los Buses')
                        ->schema([
                            Card::make()
                                ->schema([
                                    Select::make('bus_id')
                                        ->label('Bus')
                                        ->options($options)
                                        ->searchable()
                                        ->preload(),
                                    DatePicker::make('fecha_inicio')->label('Fecha inicio'),
                                    DatePicker::make('fecha_fin')->label('Fecha fin'),
                                ]),
                        ]),
                
                        
                ]),
                
        ];
    }


    public function generarReporte()
    {
       $data = $this->form->getState();

    $busId = $data['bus_id'];
    $fechaInicio = $data['fecha_inicio'];
    $fechaFin = $data['fecha_fin'];

    if (!$fechaInicio || !$fechaFin) {
        $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Debes seleccionar fechas válidas.']);
        return;
    }

    $this->iframePDF = "http://127.0.0.2:8000/geocodificar?fecha_inicio={$fechaInicio}&fecha_fin={$fechaFin}";
    }

    // Permisos de visualizacion solo usuarios con permisos
    public static function canAccess(): bool
    {
        return Gate::allows('page_CentroMonitoreo');
    }
}
