<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;

class SchemeDropdown extends Component
{
    public $schemes;
    public $schemeId = null;
    public $option = null;
    public function mount($schemes)
    {
        $this->schemes = $schemes;
        $route = Route::currentRouteName();
        if ($route == 'schemes.final-submitted') {
            $this->option = 1;
        } elseif ($route == 'age-management') {
            $this->option = 2;
        }
    }

    public function render()
    {
        return view('livewire.scheme-dropdown');
    }
}
