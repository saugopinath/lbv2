<?php

namespace App\Livewire;

use Livewire\Component;

class Loader extends Component
{
    public $loaderShow = false;

    protected $listeners = ['showLoader', 'hideLoader'];

    public function showLoader()
    {
        // dump('loaderstart');
        
        $this->loaderShow = true;
        // dump($this->show);
    }

    public function hideLoader()
    {
        // dump('insidehide');
        $this->loaderShow = false;
        // dump($this->show);
    }

    public function render()
    {
        return view('livewire.loader');
    }
}
