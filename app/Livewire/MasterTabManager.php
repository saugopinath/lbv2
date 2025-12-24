<?php

// app/Livewire/MasterTabManager.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\MasterTab;
use App\Models\SchemeTabMapping;

class MasterTabManager extends Component
{
    public $selectedSchemeId;
    public $selectedTabs = []; // Array of tab_codes
    public $positions = []; // tab_code => position

    public function mount()
    {
        // Default positions for core tabs
        $this->positions = [
            'persona' => 1,
            'contact' => 2,
            'bank' => 3,
            'encloser' => 4,
        ];
    }

    public function submit()
    {
        $this->validate([
            'selectedSchemeId' => 'required|exists:schemes,id',
            'selectedTabs' => 'required|array|min:1',
        ]);

        foreach ($this->selectedTabs as $tabCode) {
            $position = $this->positions[$tabCode] ?? max(array_values($this->positions)) + 1; // Auto for extras
            SchemeTabMapping::create([
                'scheme_id' => $this->selectedSchemeId,
                'tab_code' => $tabCode,
                'position' => $position,
            ]);
        }

        session()->flash('message', 'Tabs assigned successfully!');
        $this->reset(['selectedTabs']);
    }

    public function render()
    {
        $schemes = Scheme::where('is_active', 1)->get();
        $allTabs = MasterTab::where('is_active', true)->get();

        return view('livewire.master-tab-manager', compact('schemes', 'allTabs'));
    }
}
