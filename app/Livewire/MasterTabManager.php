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

    public $showPreview = false;

    // 🔑 Button control
    public $mappingSaved = false;

    protected $rules = [
        'selectedSchemeId' => 'required|exists:schemes,id',
        'selectedTabs'     => 'required|array|min:1',
    ];

    /* ------------------------------
     | SCHEME CHANGE
     |------------------------------*/
    public function updatedSelectedSchemeId($value)
    {
        if (blank($value)) {
            $this->reset([
                'selectedSchemeName',
                'selectedTabs',
                'positions',
                'selectedTabCode',
                'mappingSaved',
            ]);
            return;
        }

        $this->mappingSaved = false;
        $this->selectedTabs = [];
        $this->positions = [];

        $scheme = Scheme::find((int) $value);
        if (!$scheme) return;

        $this->selectedSchemeName = $scheme->name;

        $mappings = SchemeTabMapping::where('scheme_id', $value)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();

        if ($mappings->count()) {
            $this->mappingSaved = true;
        }

        foreach ($mappings as $map) {
            $this->selectedTabs[] = (int) $map->tab_code;
            $this->positions[$map->tab_code] = (int) $map->position;
        }
    }

    /* ------------------------------
     | ADD TAB
     |------------------------------*/
    public function updatedSelectedTabCode()
    {
        if (!$this->selectedTabCode) return;

        if (!in_array($this->selectedTabCode, $this->selectedTabs)) {
            $this->selectedTabs[] = (int) $this->selectedTabCode;
            $this->recalculatePositions();
        }

        $this->mappingSaved = false;
        $this->selectedTabCode = null;
    }

    /* ------------------------------
     | REMOVE TAB
     |------------------------------*/
    public function removeTab($tabCode)
    {
        $this->selectedTabs = array_values(
            array_diff($this->selectedTabs, [(int)$tabCode])
        );

        $this->recalculatePositions();
        $this->mappingSaved = false;
    }

    /* ------------------------------
     | DRAG & DROP
     |------------------------------*/
    public function updateOrder(array $ordered)
    {
        $this->selectedTabs = array_map('intval', $ordered);
        $this->recalculatePositions();
        $this->mappingSaved = false;
    }

    private function recalculatePositions()
    {
        $this->positions = [];
        foreach ($this->selectedTabs as $i => $code) {
            $this->positions[$code] = $i + 1;
        }
    }

    /* ------------------------------
     | SAVE
     |------------------------------*/
    public function submit()
    {
        $this->validate();

        DB::transaction(function () {

            SchemeTabMapping::where('scheme_id', $this->selectedSchemeId)
                ->increment('position', 1000, ['is_active' => false]);

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

        $this->mappingSaved = true;
        session()->flash('message', 'Tabs mapped successfully.');
    }

    /* ------------------------------
     | PREVIEW
     |------------------------------*/
    public function openPreview()
    {
        $this->showPreview = true;
    }

    public function closePreview()
    {
        $this->showPreview = false;
    }

    public function render()
    {
        return view('livewire.master-tab-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
            'allTabs' => MasterTab::where('is_active', true)->orderBy('tab_name')->get(),
        ]);
    }
}
