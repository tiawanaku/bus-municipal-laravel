<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class SeguimientoBus extends Component
{public $startDay;
    public $endDay;
   
    public $geoJsonData = null;
    public $title='Holii';

    public $trackingData;

    public $startDate;
    public $endDate;
    public $busData = [
        ['lat' => -16.5, 'lng' => -68.1],
        ['lat' => -16.6, 'lng' => -68.2],
    ];

    public function getTrackingData()
    {
        
        $url = "http://127.0.0.2:8000/tracking-data/?start_day={$this->startDay}&end_day={$this->endDay}";

    // Inicializa cURL
    $ch = curl_init();

    // Configura cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecuta cURL
    $response = curl_exec($ch);

    // Manejo de errores
    if(curl_errno($ch)) {
        session()->flash('error', 'Error al obtener datos de la API: ' . curl_error($ch));
        curl_close($ch);
        return;
    }

    // Cierra la conexión cURL
    curl_close($ch);

    // Decodifica la respuesta JSON
    $this->trackingData = json_decode($response, true);

    // Verifica los datos (opcional, para depuración)
    // dd($this->trackingData);

    // Notifica que los datos han sido actualizados
    $this->dispatch('trackingDataUpdated', $this->trackingData);

       
    
    }
    public $start_day = '';
    public $end_day = '';
    public $mapUrl;

    public function fetchMapData()
    {
        // Verificar si el usuario ha enviado las fechas
        if ($this->start_day && $this->end_day) {
            // Construir la URL con los parámetros start_day y end_day
            $this->mapUrl = 'http://127.0.0.2:8000/tracking-data/?start_day=' . $this->start_day . '&end_day=' . $this->end_day;
        } else {
            // Si no se han enviado las fechas, mostrar un mensaje de error
            session()->flash('error', 'Por favor, seleccione las fechas de inicio y fin.');
        }
    }

    public function render()
    {
        
        return view('livewire.seguimiento-bus', [
            
        ]);
    }

}
