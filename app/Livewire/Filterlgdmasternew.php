<?php

namespace App\Livewire;

use Livewire\Component;

class Filterlgdmasternew extends Component
{
    public $formData = [];
    public $showAssembly = false;
    public function mount($showAssembly = null)
    {
        $this->showAssembly = $showAssembly;
        $this->initializeForm();
    }
    private function initializeForm()
    {
        $this->formData = [
            'district_id' => '',
            'assemblie' => '',
            'rural_urban' => '',
            'blockurban' => '',
            'gpward' => '',
        ];
    }
    public function filterData()
    {
        $payload = $this->formData;
        if (!$this->showAssembly) {
            unset($payload['assemblie']);
        }
        $this->dispatch('filter-applied', data: $payload);
    }
    public function resetFilters()
    {
        $this->initializeForm();
        $this->dispatch('filter-cleared');
    }
    public function render()
    {
        return view('livewire.filterlgdmasternew');
    }
}
