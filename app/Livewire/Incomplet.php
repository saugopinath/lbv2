<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Incomplet extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
    public ?string $stage = null;
    public function mount($stage = null)
    {
        $this->stage = $stage;
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
        return view('livewire.incomplet');
    }
}
