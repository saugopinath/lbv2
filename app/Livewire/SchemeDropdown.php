<?php

namespace App\Livewire;

use App\Models\Scheme;
use Livewire\Component;
use Illuminate\Support\Facades\Route;

class SchemeDropdown extends Component
{
    public $schemes;
    public $schemeName;
    public $schemeId = null;
    public $option = null;
     public $button_show;
    public function mount($schemes)
    {
        $this->schemes = $schemes;
        $this->button_show = 1;

        $route = Route::currentRouteName();
        if ($route == 'schemes.final-submitted') {
            $this->option = 1;
        } elseif ($route == 'age-management') {
            $this->option = 2;
        }elseif ($route == 'lb-application-list') {
            $this->option = 3;
        }elseif ($route == 'role-office-master-mappings') {
            $this->option = 4;
        }elseif ($route == 'officemasters') {
            $this->option = 5;
        }elseif ($route == 'user-managements') {
            $this->option = 6;
        }

    }
    public function updatedSchemeId($value)
    {
        $scheme = Scheme::find($value);
        $this->schemeName = $scheme?->name;
    }
    public function render()
    {
        return view('livewire.scheme-dropdown');
    }
}
