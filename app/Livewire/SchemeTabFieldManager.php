<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabBasefield;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SchemeTabFieldManager extends Component
{
    public $schemeId;
    public $lockScheme = false;

    public $tabs = [];

    /** Tab wise selected fields
     * [
     *   tab_code => [ field_id => field_name ]
     * ]
     */
    public $tabFields = [];

    /* -------- Manage Modal -------- */
    public $showManageModal = false;
    public $activeTabCode = null;
    public $modalFields = [];
    public $previewTabName = '',$previewTabCode;

    public $modalSelected = [];
    public $showFinalPreview = false;

    /* -------- Preview Modal -------- */
    public $showPreviewModal = false;

    public function mount($scheme_id = null)
    {
        if ($scheme_id) {
            try {
                $this->schemeId = (int) Crypt::decryptString($scheme_id);
                $this->lockScheme = true;
                $this->loadTabs();
            } catch (DecryptException $e) {
                abort(403, 'Invalid scheme reference');
            }
        }
    }
    public function openFinalPreview()
    {
        $this->showFinalPreview = true;
    }

    public function closeFinalPreview()
    {
        $this->showFinalPreview = false;
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

    public function openManageModal($tabCode)
    {
        $this->activeTabCode = $tabCode;
        $this->showManageModal = true;

        $fields = SchemeTabBasefield::whereIn('scheme_id', [0, $this->schemeId])
            ->whereIn('tab_code', [$tabCode, 0])
            ->where('is_active', true)
            ->orderBy('field_name')
            ->get();

        $this->modalFields = $fields->map(fn($f) => [
            'field_id'     => $f->id,
            'field_name'   => $f->level_name,
            'is_mandatory' => (int) ($f->is_mendetory ?? 0),
        ])->toArray();

        // 🔹 1. mandatory fields
        $mandatoryIds = collect($this->modalFields)
            ->filter(fn($f) => $f['is_mandatory'] === 1)
            ->pluck('field_id')
            ->toArray();

        $previousSelected = array_keys(
            $this->tabFields[$tabCode] ?? []
        );

        $this->modalSelected = array_values(
            array_unique(
                array_merge($mandatoryIds, $previousSelected)
            )
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
     | Save Fields
     |-----------------------------*/
    public function saveManageFields()
    {
        $this->tabFields[$this->activeTabCode] = [];

        foreach ($this->modalSelected as $fid) {
            $field = collect($this->modalFields)
                ->firstWhere('field_id', $fid);

            if ($field) {
                $this->tabFields[$this->activeTabCode][$fid]
                    = $field['field_name'];
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
        }
    }

    /* -----------------------------
     | Preview
     |-----------------------------*/
    public function openPreview($tabCode)
    {
        $this->activeTabCode = $tabCode;

        $tab = collect($this->tabs)
            ->firstWhere('tab_code', $tabCode);
            // dd( $tab);

        $this->previewTabName = $tab?->masterTab?->tab_name ?? 'Preview';
        $this->previewTabCode = $tab?->masterTab?->tab_code;

        $this->showPreviewModal = true;
    }


    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->activeTabCode = null;
        $this->previewTabName = '';
    }


    public function updateFieldOrder($tabCode, $orderedIds)
    {
        if (!isset($this->tabFields[$tabCode])) return;

        $newOrder = [];

        foreach ($orderedIds as $fid) {
            if (isset($this->tabFields[$tabCode][$fid])) {
                $newOrder[$fid] = $this->tabFields[$tabCode][$fid];
            }
        }

        $this->tabFields[$tabCode] = $newOrder;
    }

    public function getPreviewFieldsProperty()
    {
        if (!$this->activeTabCode) {
            return collect();
        }

        $fieldIds = array_keys($this->tabFields[$this->activeTabCode] ?? []);

        if (empty($fieldIds)) {
            return collect();
        }

        return SchemeTabBasefield::whereIn('id', $fieldIds)
            ->whereIn('scheme_id', [0, $this->schemeId])
            ->whereIn('tab_code', [0, $this->activeTabCode])
            ->where('is_active', true)
            ->get()
            ->sortBy(fn($f) => array_search($f->id, $fieldIds))
            ->values();
    }
    public function getFinalPreviewDataProperty()
    {
        $data = [];

        foreach ($this->tabs as $tab) {
            $tabCode = $tab->tab_code;

            if (!isset($this->tabFields[$tabCode]) || empty($this->tabFields[$tabCode])) {
                continue;
            }

            $fieldIds = array_keys($this->tabFields[$tabCode]);

            $fields = SchemeTabBasefield::whereIn('id', $fieldIds)
                ->whereIn('scheme_id', [0, $this->schemeId])
                ->whereIn('tab_code', [0, $tabCode])
                ->where('is_active', true)
                ->get()
                ->sortBy(fn($f) => array_search($f->id, $fieldIds))
                ->values();

            // dd($fields);

            $data[] = [
                'tab_name' => $tab->masterTab?->tab_name,
                'fields'   => $fields,
            ];
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.scheme-tab-field-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
