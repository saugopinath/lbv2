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

    /**
     * [
     *   tab_code => [
     *      field_id => [
     *          'name' => field_name,
     *          'position' => 1
     *      ]
     *   ]
     * ]
     */
    public $tabFields = [];

    /* -------- Manage Modal -------- */
    public $showManageModal = false;
    public $activeTabCode = null;
    public $modalFields = [];
    public $modalSelected = [];

    /* -------- Preview Modal -------- */
    public $showPreviewModal = false;

    /* -----------------------------
     | Mount
     |-----------------------------*/
    public function mount($scheme_id = null)
    {
        if ($scheme_id) {
            $this->schemeId = (int) $scheme_id;
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

        $this->resetState();
        $this->loadTabs();
    }

    private function resetState()
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

    /* -----------------------------
     | Open Manage Modal
     |-----------------------------*/
    public function openManageModal($tabCode)
    {
        $this->activeTabCode = $tabCode;
        $this->showManageModal = true;

        $fields = SchemeTabBasefield::whereIn('scheme_id', [0, $this->schemeId])
            ->whereIn('tab_code', [0, $tabCode])
            ->where('is_active', true)
            ->orderBy('field_name')
            ->get();

        $this->modalFields = $fields->map(fn ($f) => [
            'field_id'   => $f->field_id,
            'field_name' => $f->field_name,
        ])->toArray();

        $this->modalSelected = array_keys(
            $this->tabFields[$tabCode] ?? []
        );
    }

    public function closeManageModal()
    {
        $this->showManageModal = false;
        $this->activeTabCode = null;
        $this->modalFields = [];
        $this->modalSelected = [];
    }

    /* -----------------------------
     | Save Selected Fields
     |-----------------------------*/
    public function saveManageFields()
    {
        $this->tabFields[$this->activeTabCode] = [];

        $pos = 1;
        foreach ($this->modalSelected as $fid) {
            $field = collect($this->modalFields)
                ->firstWhere('field_id', $fid);

            if ($field) {
                $this->tabFields[$this->activeTabCode][$fid] = [
                    'name' => $field['field_name'],
                    'position' => $pos++,
                ];
            }
        }

        $this->closeManageModal();
    }

    /* -----------------------------
     | Remove Field
     |-----------------------------*/
    public function removeField($tabCode, $fieldId)
    {
        unset($this->tabFields[$tabCode][$fieldId]);

        if (empty($this->tabFields[$tabCode])) {
            unset($this->tabFields[$tabCode]);
        } else {
            $pos = 1;
            foreach ($this->tabFields[$tabCode] as $fid => $data) {
                $this->tabFields[$tabCode][$fid]['position'] = $pos++;
            }
        }
    }

    /* -----------------------------
     | Drag & Drop Reorder
     |-----------------------------*/
    public function updateFieldOrder($tabCode, $orderedIds)
    {
        if (!isset($this->tabFields[$tabCode])) return;

        $newOrder = [];
        $pos = 1;

        foreach ($orderedIds as $fid) {
            if (isset($this->tabFields[$tabCode][$fid])) {
                $newOrder[$fid] = [
                    'name' => $this->tabFields[$tabCode][$fid]['name'],
                    'position' => $pos++,
                ];
            }
        }

        $this->tabFields[$tabCode] = $newOrder;
    }

    /* -----------------------------
     | Preview
     |-----------------------------*/
    public function openPreview($tabCode)
    {
        $this->activeTabCode = $tabCode;
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->activeTabCode = null;
    }

    public function render()
    {
        return view('livewire.scheme-tab-field-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
