<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabBasefield;

class SchemeTabFieldManager extends Component
{
    public $schemeId;
    public $lockScheme = false;

    public $tabs = [];

    /* ---------- Modal State ---------- */
    public $showManageModal = false;
    public $showPreviewModal = false;

    public $activeTabCode = null;

    public $modalFields = [];
    public $modalSelected = [];

    /* ---------- Saved Fields ---------- */
    // [tab_code => [field_id => field_name]]
    public $tabFields = [];

    /* ---------------- Mount ---------------- */
    public function mount($scheme_id = null)
    {
        if ($scheme_id) {
            $this->schemeId = (int)$scheme_id;
            $this->lockScheme = true;
            $this->loadTabs();
        }
    }

    /* ---------------- Scheme Change ---------------- */
    public function updatedSchemeId()
    {
        if ($this->lockScheme) return;

        $this->resetAll();
        $this->loadTabs();
    }

    private function resetAll()
    {
        $this->tabs = [];
        $this->tabFields = [];
        $this->activeTabCode = null;
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

    /* ---------------- Manage Fields Modal ---------------- */
    public function openManageModal($tabCode)
    {
        // dd('ok');
        $this->activeTabCode = $tabCode;
        $this->showManageModal = true;
// dd($this->showManageModal);
        $fields = SchemeTabBasefield::whereIn('scheme_id', [0, $this->schemeId])
            ->where('tab_code', $tabCode)
            ->where('is_active', true)
            ->orderBy('field_name')
            ->get();

        $this->modalFields = $fields->map(fn ($f) => [
            'field_id' => $f->field_id,
            'field_name' => $f->field_name,
        ])->toArray();

        $this->modalSelected = array_keys(
            $this->tabFields[$tabCode] ?? []
        );
    }

    public function saveManageFields()
    {
        $this->tabFields[$this->activeTabCode] = [];

        foreach ($this->modalSelected as $fieldId) {
            $field = collect($this->modalFields)
                ->firstWhere('field_id', $fieldId);

            if ($field) {
                $this->tabFields[$this->activeTabCode][$fieldId]
                    = $field['field_name'];
            }
        }

        $this->closeManageModal();
    }

    public function closeManageModal()
    {
        $this->showManageModal = false;
        $this->modalFields = [];
        $this->modalSelected = [];
    }

    /* ---------------- Remove Field ---------------- */
    public function removeField($tabCode, $fieldId)
    {
        unset($this->tabFields[$tabCode][$fieldId]);
    }

    /* ---------------- Preview ---------------- */
    public function openPreview($tabCode)
    {
        $this->activeTabCode = $tabCode;
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
    }

    public function render()
    {
        return view('livewire.scheme-tab-field-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
