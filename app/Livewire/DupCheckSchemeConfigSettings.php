<?php

namespace App\Livewire;

use App\Models\SchemeFinalSubmitCheck;
use Livewire\Component;

class DupCheckSchemeConfigSettings extends Component
{
    public $dupcheckOptions = [], $selecteddupcheckOptions = [], $schemeOptions = [], $schemes = [], $issame = 'yes', $iscross = 'no', $schemeId;
    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->dupcheckOptions = [
            '1' => 'Aadhar',
            '2' => 'Bank',
            '3' => 'Mobile',
        ];
        $this->schemeOptions = SchemeFinalSubmitCheck::where('is_final_submitted', true)
            ->where('scheme_id', '!=', $schemeId)
            ->whereHas('scheme')
            ->with('scheme')
            ->get()
            ->pluck('scheme.name', 'scheme.id')
            ->toArray();
    }

    public function toggleAll()
    {
        if (count($this->selecteddupcheckOptions) === count($this->dupcheckOptions)) {
            $this->selecteddupcheckOptions = [];
        } else {
            $this->selecteddupcheckOptions = array_keys($this->dupcheckOptions);
        }
    }

    public function save()
    {
        dd( $this->selecteddupcheckOptions);
        $validated = $this->validate([
            'issame' => 'required',
            'iscross' => 'required',
            'selecteddupcheckOptions' => 'required|array|min:1',
            'schemes' => 'required_if:iscross,yes',
        ]);
    }

    public function render()
    {
        return view('livewire.dup-check-scheme-config-settings');
    }
}
