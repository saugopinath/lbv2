<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Filterlgdmasternew1 extends Component
{
    #[On('filter-applied')]
    public function filterApplied($filters)
    {
        dd($filters);
    }
    #[On('filter-cleared')]
    public function filterCleared()
    {
        // dd('ok');
    }
    #[On('beneficiary-search')]
    public function beneficiarySearched($data)
    {
        dd($data);
    }
    public function render()
    {
        return view('livewire.filterlgdmasternew1');
    }
}
