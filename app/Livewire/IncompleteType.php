<?php

namespace App\Livewire;

use App\Models\Codemaster;
use Livewire\Component;

class IncompleteType extends Component
{
    public $results = [];
    public $incompleteList = '';
    public $button_show = 1;

    protected $listeners = [
        'resetChildFilters' => 'resetIncompleteFilters'
    ];

    public function mount($button_show = null)
    {
        $this->button_show = $button_show ?? 1;

        $officetype = Codemaster::getIdByCode(14);
        $this->results = Codemaster::where('parent_id', $officetype)
            ->whereIn('code', [141,142,145,146,149,1410,1411,1412,1413,1414])
            ->get();
    }

    public function updatedIncompleteList($value)
    {
        $this->incompleteList = $value;
        // dd($this->incompleteList);
        $this->dispatch('filterIncompleteType', $this->incompleteList);
    }

    public function search()
    {
        $this->dispatch('filterIncompleteType', $this->incompleteList);
    }

    public function resetIncompleteFilters()
    {
        $this->incompleteList = '';
        $this->dispatch('filterIncompleteType', null);
    }

    public function render()
    {
        return view('livewire.incomplete-type');
    }
}
