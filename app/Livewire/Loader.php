<?php

namespace App\Livewire;

use Livewire\Component;

class Loader extends Component
{
    public $show = false;

    protected $listeners = ['showLoader', 'hideLoader'];

    public function showLoader()
    {
        // dump('show');
        $this->show = true;
    }

    public function hideLoader()
    {
        // dump('hide');
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.loader');
    }
}
