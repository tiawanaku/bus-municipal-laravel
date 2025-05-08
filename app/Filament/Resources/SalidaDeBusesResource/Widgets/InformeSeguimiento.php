<?php

namespace App\Filament\Resources\SalidaDeBusesResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Validator;

class InformeSeguimiento extends Widget
{
    protected static string $view = 'filament.resources.salida-de-buses-resource.widgets.informe-seguimiento';


    public $fechaInicio;
    public $fechaFin;
    public $iframeUrl;

    public function mount()
    {
        // Fechas predeterminadas 
        $this->fechaInicio = now()->subMonth()->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
        $this->iframeUrl = ''; 
    }

    // Método para actualizar el iframe con el reporte
    public function generarReporte()
    {
        
        // Validación de que ambas fechas son proporcionadas
        if (empty($this->fechaInicio) || empty($this->fechaFin)) {
            $this->addError('fechas', 'Ambas fechas son obligatorias.');
            return;
        }

        // Validación de que la fecha de fin no sea menor que la fecha de inicio
        if ($this->fechaFin < $this->fechaInicio) {
            $this->addError('fechas', 'La fecha de fin no puede ser anterior a la fecha de inicio.');
            return;
        }

        // Si las fechas son válidas, generar la URL del reporte
        $apiUrl = "http://127.0.0.2:8000/geocodificar?fecha_inicio={$this->fechaInicio}&fecha_fin={$this->fechaFin}";
        $this->iframeUrl = $apiUrl; 
    }

    public static function getWidgets(): array
    {
        return [
            self::class,
        ];
    }
}
