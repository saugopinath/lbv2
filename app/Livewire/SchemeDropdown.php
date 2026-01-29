<?php

namespace App\Livewire;

use Livewire\Component;

class SchemeDropdown extends Component
{
    public $schemes;
    public $schemeId = null;

    public function mount($schemes)
    {
        $this->schemes = $schemes;
    }

    public function render()
    {
        return view('livewire.scheme-dropdown');
    }
}
