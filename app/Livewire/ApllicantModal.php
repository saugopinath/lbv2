<?php

namespace App\Livewire;

use Livewire\Component;

class ApllicantModal extends Component
{
    // public bool $openModal = false;
    public $application_id, $openModal;
    // protected $listeners = ['selfDec' => 'open'];
    public function mount($application_id, $openModal)
    {
        $this->application_id = $application_id;
        $this->openModal = $openModal;
    }

    // public function open()
    // {
    //     $this->openModal = true;
    // }

    public function close()
    {
        $this->openModal = false;
    }

    public function render()
    {
        return view('livewire.apllicant-modal');
    }
}
