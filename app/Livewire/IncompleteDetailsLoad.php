<?php

namespace App\Livewire;
use Livewire\Attributes\On;
use Livewire\Component;

class IncompleteDetailsLoad extends Component
{
    public $schemeId = null;
    public $stage = null;

    public function mount($stage)
    {
        $this->stage = $stage;       
    }

    #[On('selectedScheme')]
    public function selectedScheme($value)
    {
        $this->schemeId = $value;
        // dump($value);
    }

    public function render()
    {
        return view('livewire.incomplete-details-load');
    }
}
