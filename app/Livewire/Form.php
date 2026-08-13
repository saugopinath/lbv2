<?php

namespace App\Livewire;

use App\Models\Scheme;
use Livewire\Component;
use Livewire\Attributes\On;

class Form extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
    public $showSchemeDropdown = true;
    public $grievanceId;
    public function mount($hideSchemeDropdown = false)
    {
        if ($hideSchemeDropdown) {
            $this->showSchemeDropdown = false;
            $schemeData = Scheme::where('is_active', 1)->first();
            $this->schemeId = $schemeData->id;
            $this->schemeData = true;
            $this->schemeName = $schemeData->name;
        }
        if (request()->has('id')) {
            $this->grievanceId = request()->query('id');
        }
    }
    #[On('selectedScheme')]
    public function updateschemeData($schemeData)
    {
        if ($schemeData) {
            $this->schemeData = true;
            $this->schemeId = $schemeData['scheme_id'];
            $this->schemeName = $schemeData['scheme_name'];
        } else {
            $this->schemeData = false;
        }
    }
    public function render()
    {
        return view('livewire.form');
    }
}
