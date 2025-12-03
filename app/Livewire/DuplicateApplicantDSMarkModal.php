<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
class DuplicateApplicantDSMarkModal extends Component
{
    public $applicantId;
    #[On('opendsMarkModal')]
    public function openModal($id = null)
    {
        $this->dispatch('show-modal');
    }
    public function render()
    {
        return view('livewire.duplicate-applicant-d-s-mark-modal');
    }
}
