<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\MasterTab;
use App\Models\SchemeAttachedDocMappings;
use App\Models\SchemeFinalSubmitCheck;
use App\Models\SchemeTabFormField;
use App\Models\SchemeTabMapping;
use App\Models\SelfDeclerationBasefield;
use Illuminate\Support\Facades\DB;

class MasterTabManager extends Component
{
    public $selectedSchemeId;
    public $selectedSchemeName;

    public $selectedTabs = [];
    public $positions = [];
    public $selectedTabCode = null;
    public $showPreview = false;
    public $mappingSaved = false;
    public $isFinalSubmitted = false;

    protected $rules = [
        'selectedSchemeId' => 'required|exists:schemes,id',
        'selectedTabs'     => 'required|array|min:1',
    ];


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
        $this->syncFinalSubmitStatus();
    }
    public function updatedSelectedTabCode()
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission has been completed.',
            ]);
            return;
        }
        if (!$this->selectedTabCode) return;

        if (!in_array($this->selectedTabCode, $this->selectedTabs)) {
            $this->selectedTabs[] = (int) $this->selectedTabCode;
            $this->recalculatePositions();
        }
        $this->mappingSaved = false;
        $this->selectedTabCode = null;
    }

    public function removeTab($tabCode)
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission has been completed.',
            ]);
            return;
        }
        $this->selectedTabs = array_values(
            array_diff($this->selectedTabs, [(int)$tabCode])
        );
        $this->recalculatePositions();
        DB::transaction(function () use ($tabCode) {

            SchemeTabMapping::where('scheme_id', $this->selectedSchemeId)
                ->where('tab_code', $tabCode)
                ->delete();
            if ($tabCode == 104) {
                SchemeAttachedDocMappings::where('scheme_id', $this->selectedSchemeId)
                    ->where('tab_code', $tabCode)
                    ->delete();
            } else if ($tabCode == 105) {
                SelfDeclerationBasefield::where('scheme_id', $this->selectedSchemeId)
                    ->where('tab_code', $tabCode)
                    ->delete();
            } else {
                SchemeTabFormField::where('scheme_id', $this->selectedSchemeId)
                    ->where('tab_code', $tabCode)
                    ->delete();
            }
        });
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Tab removed successfully.',
        ]);

        $this->mappingSaved = false;
    }

    public function updateOrder(array $ordered)
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission has been completed.',
            ]);
            return;
        }
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

    public function submit()
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission has been completed.',
            ]);
            return;
        }
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
    public function openPreview()
    {
        $this->showPreview = true;
    }

    public function closePreview()
    {
        $this->showPreview = false;
    }
    protected function syncFinalSubmitStatus(): void
    {
        $this->isFinalSubmitted = SchemeFinalSubmitCheck::where('scheme_id', $this->selectedSchemeId)
            ->where('is_final_submitted', true)
            ->exists();
    }

    public function render()
    {
        return view('livewire.master-tab-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
            'allTabs' => MasterTab::where('is_active', true)->orderBy('tab_name')->get(),
        ]);
    }
}
