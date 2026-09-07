<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Scheme;

class SchemeDropdownNew extends Component
{
    public $isFinal = false;
    public $isAssigned = false;
    public $schemes = [];
    public $schemeId;
    public $schemeSelected = false;

    #[On('resetSchemeDropdown')]
    public function resetDropdown()
    {
        $this->reset(['schemeId', 'schemeSelected']);
    }
    public function mount($isFinal = false, $isAssigned = false)
    {
        $scheme_id = null;
        if ($isAssigned) {
            $select_lgd = session('lgd_session');

            if (!empty($select_lgd['scheme_id'])) {
                // $scheme_id = Crypt::decryptString($select_lgd['scheme_id']);
                $scheme_id = Crypt::decryptString($select_lgd['scheme_id'][0]);
            }
        }

        $query = Scheme::select('id', 'name')
            ->where('is_active', 1)
            ->when($scheme_id, fn($q) => $q->where('id', $scheme_id));
        // Commented for development
        // if ($isFinal) {
        //     $query->whereHas('schemeFinalSubmitChecks', function ($q) {
        //         $q->where('is_final_submitted', true);
        //     });
        // }

        $this->schemes = $query->get();
    }
    public function updatedSchemeId($value)
    {
        if ($value) {
            $this->schemeSelected = true;
            $schemeName = Scheme::find($value)->name;
            $schemeData = ['scheme_id' => $value, 'scheme_name' => $schemeName];
            $this->dispatch('selectedScheme', $schemeData);
        } else {
            $this->schemeSelected = false;
            $this->dispatch('selectedScheme', null);
        }
    }
    public function render()
    {
        return view('livewire.scheme-dropdown-new');
    }
}
