<?php

namespace App\Livewire;

use Livewire\Component;

class ModalSeguimiento extends Component
{
    public function render()
    {
        return view('livewire.modal-seguimiento');
    }
    public $isOpen = false;

  

    protected $listeners = ['openModal' => 'open']; // Escuchar evento


    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }
}
