<?php

namespace App\Livewire;

use Livewire\Component;

class ApllicantModal extends Component
{
    public bool $openModal = false;
    public $application_id;
    protected $listeners = ['selfDec' => 'open'];
    public function mount($application_id)
    {
        $this->application_id = $application_id;
    }

    public function open()
    {
        $this->openModal = true;
    }

    public function close()
    {
        $this->openModal = false;
    }

    public function render()
    {
        return view('livewire.apllicant-modal');
    }
}
