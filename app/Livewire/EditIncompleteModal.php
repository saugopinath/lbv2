<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; // ⬅️ গুরুত্বপূর্ণ
use App\Models\ApplicantIncompletDeatil;

class EditIncompleteModal extends Component
{
    public $show = false;
    public $applicationId;
    public $data = [];

    #[On('openEditModal')]                 // ⬅️ v3 attribute listener
    public function open($applicationId)   // ⬅️ payload key-এর নামের সাথে param মেলানো
    {
        $this->applicationId = $applicationId;

        $this->data = ApplicantIncompletDeatil::where('application_id', $applicationId)
            ->with('incompletType')
            ->get()
            ->toArray(); // ⬅️ Livewire-safe array, collection নয়

        $this->show = true;
    }

    public function close()
    {
        $this->reset(['show', 'applicationId', 'data']);
    }

    public function render()
    {
        return view('livewire.edit-incomplete-modal');
    }
}
