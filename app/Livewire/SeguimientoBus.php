<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class SeguimientoBus extends Component
{public $startDay;
    public $endDay;
   
    public $geoJsonData = null;
    public $title;

    

   

    public function render()
    {
        
        return view('livewire.seguimiento-bus', [
            
        ]);
    }
    public $mostrarModal = false;

    public function abrirModal()
    {
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
    }
}
?>