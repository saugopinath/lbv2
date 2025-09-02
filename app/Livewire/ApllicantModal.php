<?php

namespace App\Livewire;

use Livewire\Component;

class ApllicantModal extends Component
{
    public $application_id;
    public bool $openModal = false;
    protected $listeners = ['openApplicantModal' => 'open'];
    public function mount($application_id = null)
    {
        $this->application_id = $application_id;
        $this->openModal = true;
    }
    // public function open()
    // {
    //     $this->openModal = true;
    // }
    public function close()
    {
        // $this->openModal = false;
        $this->dispatch('modalClosed');
    }
    public function render()
    {
        return view('livewire.apllicant-modal');
    }
}