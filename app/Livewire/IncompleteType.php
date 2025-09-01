<?php

namespace App\Livewire;

use App\Models\ApplicantIncompletDeatil;
use App\Models\Codemaster;
use Livewire\Component;

class IncompleteType extends Component
{
    public $searchType = '';
    public $searchValue = '';
    public $results = [];

    public $showModal = false;
    public $selectedRecord;
    public $newValue;
    public $codemasterOptions = [];

    public function search()
    {
        if ($this->searchType && $this->searchValue) {
            $query = ApplicantIncompletDeatil::with('incompletType')
                ->where('next_level_request_id', 1);

            if ($this->searchType === 'application_id') {
                $query->where('application_id', $this->searchValue);
            }

            if ($this->searchType === 'beneficiary_id') {
                $query->where('beneficiary_id', $this->searchValue);
            }

            $this->results = $query->get();
        } else {
            $this->results = [];
        }
    }


    public function openModal($id)
    {
        $this->selectedRecord = ApplicantIncompletDeatil::with('incompletType')->find($id);
        $this->newValue = null;

        $this->showModal = true;
    }

    public function saveUpdate()
    {
        if ($this->selectedRecord && $this->newValue) {
            $this->selectedRecord->new_value = $this->newValue;

            $this->selectedRecord->next_level_request_id = 2;

            $this->selectedRecord->save();

            $this->showModal = false;
            session()->flash('success', "{$this->newValue} updated successfully!");
            $this->search();
        }
    }


    public function closeModal()
    {
        $this->reset(['showModal', 'selectedRecord', 'newValue']);
    }

    public function render()
    {
        return view('livewire.incomplete-type');
    }
}
