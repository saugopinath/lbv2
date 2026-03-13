<?php

namespace App\Livewire;

use App\Helpers\SchemeCapacityHelper;
use App\Models\Scheme;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class SchemeDropdown extends Component
{
    public $schemes;

    public $schemeName;

    public $schemeId = null;

    public $option = null;

    public $button_show;

    public $currentRoute;

    public function mount($schemes, $scheme_id = null)
    {
        $this->schemes = $schemes;

        $this->button_show = 1;

        $this->currentRoute = Route::currentRouteName();

        if ($this->currentRoute === 'schemes.final-submitted') {
            $this->option = 1;
        } elseif ($this->currentRoute === 'define-workflow') {
            $this->option = 2;
        } elseif ($this->currentRoute === 'lb-application-list') {
            $this->option = 3;
        }

        if ($scheme_id) {
            try {

                $this->schemeId = Crypt::decryptString($scheme_id);

                $this->schemeName = Scheme::where('id', $this->schemeId)->value('name');
            } catch (\Exception $e) {

                $this->schemeId = null;

                $this->schemeName = null;
            }
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
