<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class GrievanceSearch extends Component
{
    public $selectedOption = 'Mobile Number';
    public $inputValue = '';
    public $initialMobile = '';
    public $grievanceId = '';

    #[On('searchTriggered')]
    public function handleSearchTriggered($selectedOption, $inputValue)
    {
        // এখন এগুলো ঠিকভাবে পাওয়া যাবে
        $this->selectedOption = $selectedOption;
        $this->inputValue = $inputValue;

        dd($selectedOption, $inputValue);
    }

    public function mount($initialMobile = '', $grievanceId = '')
    {
        $this->initialMobile = $initialMobile;
        $this->grievanceId = $grievanceId;
        $this->inputValue = $initialMobile;
    }

    public function render()
    {
        return view('livewire.grievance-search');
    }
}
