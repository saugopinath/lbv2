<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;

class SchemeDropdownNew extends Component
{
    public $isFinal = false;
    public $schemes = [];
    public $schemeId;
    public function mount($isFinal = false)
    {
        if ($isFinal) {
            $this->schemes = Scheme::whereHas('schemeFinalSubmitChecks', function ($query) {
                $query->where('is_final_submitted', true);
            })->get();
        } else {
            $this->schemes = Scheme::all();
        }
    }
    public function updatedSchemeId($value)
    {
        // if (!$value) {
        //     return;
        // }
        $this->dispatch('selectedScheme', $value);
    }
    public function render()
    {
        return view('livewire.scheme-dropdown-new');
    }
}
