<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\MasterTab;
use App\Models\SchemeTabMapping;
use Illuminate\Support\Facades\DB;

class MasterTabManager extends Component
{
    public $selectedSchemeId;
    public $selectedSchemeName;

    public $selectedTabs = [];
    public $positions = [];
    public $selectedTabCode = null;

    protected $rules = [
        'selectedSchemeId' => 'required|exists:schemes,id',
        'selectedTabs'     => 'required|array|min:1',
    ];

    /* ------------------------------
     |  SCHEME CHANGE
     |------------------------------*/
    public function updatedSelectedSchemeId($value)
    {
        if (blank($value)) {
            $this->reset([
                'selectedSchemeName',
                'selectedTabs',
                'positions',
                'selectedTabCode',
            ]);
            return;
        }

        $this->selectedTabs = [];
        $this->positions = [];

        $scheme = Scheme::find((int) $value);
        if (!$scheme) {
            return;
        }

        $this->selectedSchemeName = $scheme->name;

        $mappings = SchemeTabMapping::where('scheme_id', (int)$value)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();

        foreach ($mappings as $map) {
            $this->selectedTabs[] = (int)$map->tab_code;
            $this->positions[$map->tab_code] = (int)$map->position;
        }
    }

    /* ------------------------------
     |  ADD TAB
     |------------------------------*/
    public function updatedSelectedTabCode()
    {
        if (!$this->selectedTabCode) {
            return;
        }

        if (!in_array((int)$this->selectedTabCode, $this->selectedTabs)) {
            $this->selectedTabs[] = (int)$this->selectedTabCode;
            $this->recalculatePositions();
        }

        $this->selectedTabCode = null;
    }

    /* ------------------------------
     |  REMOVE TAB
     |------------------------------*/
    public function removeTab($tabCode)
    {
        $this->selectedTabs = array_values(
            array_diff($this->selectedTabs, [(int)$tabCode])
        );

        $this->recalculatePositions();
    }

    /* ------------------------------
     |  DRAG & DROP ORDER UPDATE
     |------------------------------*/
    public function updateOrder(array $orderedTabCodes)
    {
        $this->selectedTabs = array_map('intval', $orderedTabCodes);
        $this->recalculatePositions();
    }

    /* ------------------------------
     |  AUTO POSITION (1,2,3...)
     |------------------------------*/
    private function recalculatePositions(): void
    {
        $this->positions = [];
        foreach ($this->selectedTabs as $index => $tabCode) {
            $this->positions[$tabCode] = $index + 1;
        }
    }

    /* ------------------------------
     |  SAVE
     |------------------------------*/
    public function submit()
    {
        $this->validate();

        DB::transaction(function () {

            // TEMP OFFSET (safe for UNIQUE + NOT NULL)
            SchemeTabMapping::where('scheme_id', $this->selectedSchemeId)
                ->increment('position', 1000, [
                    'is_active' => false,
                ]);

            foreach ($this->selectedTabs as $tabCode) {
                SchemeTabMapping::updateOrCreate(
                    [
                        'scheme_id' => $this->selectedSchemeId,
                        'tab_code'  => $tabCode,
                    ],
                    [
                        'position'  => $this->positions[$tabCode],
                        'is_active' => true,
                    ]
                );
            }
        });
        $this->reset([
            'selectedSchemeId',
            'selectedSchemeName',
            'selectedTabs',
            'positions',
            'selectedTabCode',
        ]);
        session()->flash('message', 'Tabs mapped successfully.');
    }

    public function render()
    {
        return view('livewire.master-tab-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
            'allTabs' => MasterTab::where('is_active', true)->orderBy('tab_name')->get(),
        ]);
    }
}
