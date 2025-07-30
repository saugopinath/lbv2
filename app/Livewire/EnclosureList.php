<?php

namespace App\Livewire;

use Livewire\Component;

class EnclosureList extends Component
{
    public $mode;
    public function mount($mode = null)
    {
        $this->mode = $mode;
    }
    public function render()
    {
        return view('livewire.enclosure-list');
    }
}
