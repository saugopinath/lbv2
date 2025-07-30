<?php

namespace App\Livewire;

use Livewire\Component;

class ContactDetails extends Component
{
    public $mode;
    public function mount($mode = null)
    {
        $this->mode = $mode;
    }
    public function render()
    {
        return view('livewire.contact-details');
    }
}
