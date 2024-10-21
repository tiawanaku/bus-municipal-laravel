<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class SeguimientoBus extends Component
{public $startDay;
    public $endDay;
    public $trackingData = [];
    public $puntosPorFecha = [];
    

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
   /*  dd($this->trackingData); */

    // Notifica que los datos han sido actualizados
    $this->dispatch('trackingData', $this->trackingData);
       
    }
    public function render()
    {
        return view('livewire.seguimiento-bus');
    }
}
