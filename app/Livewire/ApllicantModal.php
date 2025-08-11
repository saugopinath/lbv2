<?php

namespace App\Livewire;

use Livewire\Component;

class ApllicantModal extends Component
{
    public bool $openModal = true;
    public $id;

    public function mount($id)
    {
        $this->id = $id;
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
