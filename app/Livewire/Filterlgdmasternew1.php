<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
class Filterlgdmasternew1 extends Component
{
    #[On('filter-applied')]
    public function filterApplied($filters) {
        dd($filters);
    }
    #[On('filter-cleared')]
    public function filterCleared() {
        // dd('ok');
    }
    public function render()
    {
        return view('livewire.filterlgdmasternew1');
    }
}
