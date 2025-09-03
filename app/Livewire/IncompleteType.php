<?php

namespace App\Livewire;

use App\Models\ApplicantIncompletDeatil;
use App\Models\Codemaster;
use Livewire\Component;

class IncompleteType extends Component
{
    public $results = [];
    public $incompleteList = '';

    public function mount()
    {
        $officetype = Codemaster::getIdByCode(14);
        $this->results = Codemaster::where('parent_id', $officetype)
            ->whereIn('code', [141, 142, 143, 144, 145, 146, 147, 148, 149, 1410, 1411, 1412, 1413, 1414])->get();
    }

    public function search()
    {
        $this->dispatch('filterIncompleteType', code: $this->incompleteList);
    }

    public function render()
    {
        return view('livewire.incomplete-type');
    }
}
