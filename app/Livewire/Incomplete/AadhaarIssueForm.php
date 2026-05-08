<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;

class AadhaarIssueForm extends Component
{
    public $item;


    public function mount($item)
    {
        dd('ok');
        $this->item = $item;
    }




    public function render()
    {
        return view('livewire.incomplete.aadhaar-issue-form');
    }
}
