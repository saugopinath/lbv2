<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;

class MobileIssueForm extends Component
{
    public $item;

    public function mount($item, $mobileIssues)
    {
        $this->item = $item;
    }


    public function render()
    {
        return view('livewire.incomplete.mobile-issue-form');
    }
}
