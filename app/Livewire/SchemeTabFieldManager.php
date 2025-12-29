<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabBasefield;

class SchemeTabFieldManager extends Component
{
    public $schemeId;
    public $lockScheme = false; // 🔒 dropdown control

    public $tabs = [];
    public $activeTabCode = null;

    public $availableFields = [];
    public $selectedFields = [];

    /* -----------------------------
     | Mount (Route param handle)
     |-----------------------------*/
    public function mount($scheme_id = null)
    {
        if ($scheme_id) {
            $this->schemeId   = (int) $scheme_id;
            $this->lockScheme = true;
            $this->loadTabs();
        }
    }

    /* -----------------------------
     | Scheme Change
     |-----------------------------*/
    public function updatedSchemeId()
    {
        if ($this->lockScheme) return;

        $this->resetTabState();
        $this->loadTabs();
    }

    private function loadTabs()
    {
        if (!$this->schemeId) return;

        $this->tabs = SchemeTabMapping::with('masterTab')
            ->where('scheme_id', $this->schemeId)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
    }

    private function resetTabState()
    {
        $this->tabs = [];
        $this->activeTabCode = null;
        $this->availableFields = [];
        $this->selectedFields = [];
    }

    /* -----------------------------
     | Tab Select
     |-----------------------------*/
    public function selectTab($tabCode)
    {
        $this->activeTabCode = $tabCode;
        $this->selectedFields = [];

        $baseFields = SchemeTabBasefield::whereIn('scheme_id', [0, $this->schemeId])
            ->where('tab_code', $tabCode)
            ->where('is_active', true)
            ->orderBy('field_name')
            ->get();

        $this->availableFields = $baseFields->map(fn ($f) => (object)[
            'field_id'   => $f->field_id,
            'field_name' => $f->field_name,
        ])->toArray();
    }

    /* -----------------------------
     | Add Field
     |-----------------------------*/
    public function addField($fieldId)
    {
        $field = collect($this->availableFields)
            ->firstWhere('field_id', $fieldId);

        if (!$field) return;

        $this->selectedFields[$fieldId] = $field->field_name;

        $this->availableFields = collect($this->availableFields)
            ->reject(fn ($f) => $f->field_id == $fieldId)
            ->values()
            ->toArray();
    }

    /* -----------------------------
     | Remove Field
     |-----------------------------*/
    public function removeField($fieldId)
    {
        if (!isset($this->selectedFields[$fieldId])) return;

        $this->availableFields[] = (object)[
            'field_id'   => $fieldId,
            'field_name' => $this->selectedFields[$fieldId],
        ];

        unset($this->selectedFields[$fieldId]);
    }

    public function render()
    {
        return view('livewire.scheme-tab-field-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
