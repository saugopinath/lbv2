<?php

namespace App\Livewire;

use App\Models\Scheme;
use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class SchemeDropdown extends Component
{
    public $schemes;
    public $schemeName;
    public $schemeId = null;
    public $option = null;
    public $button_show, $stage;
    public function mount($schemes, $scheme_id = null, $stage = null)
    {
        $this->stage = $stage;
        $this->schemes = $schemes;
        $this->button_show = 1;

        $route = Route::currentRouteName();
        if ($route == 'schemes.final-submitted') {
            $this->option = 1;
        } elseif ($route == 'define-workflow') {
            $this->option = 2;
        } elseif ($route == 'lb-application-list') {
            $this->option = 3;
        }elseif ($route == 'incomplete.types') {
            $this->option = 4;
        }

        if ($scheme_id) {
            try {
                $this->schemeId = Crypt::decryptString($scheme_id);
                $this->schemeName = Scheme::where('id', $this->schemeId)->value('name');
            } catch (\Exception $e) {
                $this->schemeId = null;
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
