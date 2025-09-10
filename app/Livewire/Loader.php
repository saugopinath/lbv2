<?php

namespace App\Livewire;

use Livewire\Component;

class Loader extends Component
{
    public $show = false;

    protected $listeners = ['showLoader', 'hideLoader'];

    public function showLoader()
    {
        
        $this->show = true;
    }

    public function hideLoader()
    {
        
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.loader');
    }
}
