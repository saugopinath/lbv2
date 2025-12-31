<?php

namespace App\Livewire;

use App\Models\Codemaster;
use Livewire\Component;
use App\Models\Scheme;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeTabFieldTemp;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;

class SchemeTabFieldManager extends Component
{
    public $schemeId, $lockScheme = false, $tabs = [], $tabFields = [], $showManageModal = false,
        $activeTabCode = null, $modalFields = [], $previewTabName = '', $previewTabCode = null,
        $modalSelected = [], $showFinalPreview = false, $showPreviewModal = false,
        $finalActiveTabCode = null;
    public $finalPreviewFields = [];


    public function mount(Request $request)
    {
        $scheme_id = $request->query('scheme_id');
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

        $firstTab = collect($this->tabs)->first();
        $this->finalActiveTabCode = $firstTab?->tab_code;

        $this->loadFinalPreviewFields();
    }
    public function setFinalPreviewTab($tabCode)
    {
        $this->finalActiveTabCode = $tabCode;
        $this->loadFinalPreviewFields();
    }
    public function closeFinalPreview()
    {
        $this->showFinalPreview = false;
        $this->finalActiveTabCode = null;
        $this->finalPreviewFields = collect();
    }
    private function loadFinalPreviewFields()
    {
        if (!$this->finalActiveTabCode) {
            $this->finalPreviewFields = collect();
            return;
        }

        $fieldIds = array_keys($this->tabFields[$this->finalActiveTabCode] ?? []);

        if (empty($fieldIds)) {
            $this->finalPreviewFields = collect();
            return;
        }
        $this->finalPreviewFields = SchemeTabBasefield::whereIn('id', $fieldIds)
            ->whereIn('scheme_id', [0, $this->schemeId])
            ->whereIn('tab_code', [0, $this->finalActiveTabCode])
            ->where('is_active', true)
            ->get()
            ->sortBy(fn($f) => array_search($f->id, $fieldIds))
            ->values();
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
        // dd('fsf');
        if (!$this->schemeId) return;
        $this->tabs = SchemeTabMapping::with('masterTab')
            ->where('scheme_id', $this->schemeId)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
        $this->hydrateTabFieldsFromTemp();
    }
    private function hydrateTabFieldsFromTemp(): void
    {
        $temps = SchemeTabFieldTemp::where('scheme_id', $this->schemeId)->get();
        // dd($temps);
        foreach ($temps as $temp) {
            $tab_code = $temp->tab_code;
            // dd($tab);
            $raw = $temp->field_ids;
            if (empty($raw) || !is_array($raw)) {
                continue;
            }
            $fieldIds = collect($raw)->pluck('field_id')->toArray();
            // Resolve names
            if ($tab_code==104) {
                $names = Codemaster::whereIn('id', $fieldIds)
                    ->pluck('name', 'id');
            } else {
                $names = SchemeTabBasefield::whereIn('id', $fieldIds)
                    ->pluck('level_name', 'id');
            }
            // Order by position
            $ordered = collect($raw)
                ->sortBy('position')
                ->mapWithKeys(fn($row) => [
                    $row['field_id'] => $names[$row['field_id']] ?? 'Unknown'
                ])
                ->toArray();

            $this->tabFields[$temp->tab_code] = $ordered;
        }
    }

    /* -----------------------------
     | Open Manage Modal
     |-----------------------------*/
    // public function openManageModal($tabCode)
    // {
    //     $this->activeTabCode = $tabCode;
    //     $this->showManageModal = true;

    //     $fields = SchemeTabBasefield::whereIn('scheme_id', [0, $this->schemeId])
    //         ->whereIn('tab_code', [$tabCode, 0])
    //         ->where('is_active', true)
    //         ->orderBy('field_position')
    //         ->get();

    //     $this->modalFields = $fields
    //         ->filter(function ($field) use ($tabCode) {
    //             if ($field->tab_code == 0 && $field->is_mendetory == 1) {
    //                 if ($this->isGlobalMandatoryUsed($field->id, $tabCode)) {
    //                     return false;
    //                 }
    //             }
    //             return true;
    //         })
    //         ->map(fn($f) => [
    //             'field_id'     => $f->id,
    //             'field_name'   => $f->level_name,
    //             'is_mandatory' => $f->is_mendetory,
    //             'tab_code' => $f->tab_code
    //         ])
    //         ->toArray();

    //     $mandatoryIds = collect($this->modalFields)
    //         ->filter(fn($f) => $f['is_mandatory'] == 1 &&  $f['tab_code'] != 0)
    //         ->pluck('field_id')
    //         ->toArray();

    //     $existing = array_keys($this->tabFields[$tabCode] ?? []);

    //     $this->modalSelected = array_values(
    //         array_unique(array_merge($existing, $mandatoryIds))
    //     );
    // }
    // After save in temp table
    public function openManageModal($tabCode)
    {
        $this->activeTabCode = $tabCode;
        $this->showManageModal = true;
        // dd($this->activeTabCode);
        if ($this->activeTabCode == 104) {
            // dd('ughg');
            $this->modalFields = Codemaster::where('parent_id', 16)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get()
                // dd($this->modalFields);
                ->map(fn($c) => [
                    'field_id'     => $c->id,
                    'field_name'   => $c->name,
                    'is_mandatory' => 0,
                    'tab_code'     => 104,
                ])
                ->toArray();
            // dd($this->modalFields);
            $this->modalSelected = array_keys($this->tabFields[$tabCode] ?? []);
            return;
        }
        $fields = SchemeTabBasefield::whereIn('scheme_id', [0, $this->schemeId])
            ->whereIn('tab_code', [$tabCode, 0])
            ->where('is_active', true)
            ->orderBy('field_position')
            ->get();

        $this->modalFields = $fields
            ->filter(function ($field) use ($tabCode) {
                if ($field->tab_code == 0 && $field->is_mendetory == 1) {
                    if ($this->isGlobalMandatoryUsed($field->id, $tabCode)) {
                        return false;
                    }
                }
                return true;
            })
            ->map(fn($f) => [
                'field_id'     => $f->id,
                'field_name'   => $f->level_name,
                'is_mandatory' => $f->is_mendetory,
                'tab_code'     => $f->tab_code
            ])
            ->toArray();

        $mandatoryIds = collect($this->modalFields)
            ->filter(fn($f) => $f['is_mandatory'] == 1 && $f['tab_code'] != 0)
            ->pluck('field_id')
            ->toArray();

        // 🔽 ADD THIS
        $temp = SchemeTabFieldTemp::where('scheme_id', $this->schemeId)
            ->where('tab_code', $tabCode)
            ->first();

        $savedIds = collect($temp?->field_ids ?? [])
            ->sortBy('position')
            ->pluck('field_id')
            ->toArray();

        // 🔽 REPLACE THIS
        $existing = !empty($savedIds)
            ? $savedIds
            : array_keys($this->tabFields[$tabCode] ?? []);

        $this->modalSelected = array_values(
            array_unique(array_merge($existing, $mandatoryIds))
        );
    }

    public function isFieldMandatory($fieldId)
    {
        // dd('bhjbc');
        return SchemeTabBasefield::where('id', $fieldId)
            ->where('is_mendetory', 1)
            ->where('tab_code', '!=', 0)
            ->exists();
    }
    public function isGlobalMandatoryUsed(int $fieldId, int $currentTabCode): bool
    {
        foreach ($this->tabFields as $tabCode => $fields) {
            if ((int)$tabCode === (int)$currentTabCode) {
                continue;
            }

            if (array_key_exists($fieldId, $fields)) {
                return true;
            }
        }

        return false;
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
        // dd($this->modalSelected);
        $payload = [];

        foreach ($this->modalSelected as $index => $fid) {
            $payload[] = [
                'field_id' => (int) $fid,
                'position' => $index + 1,
            ];
        }
        // dd($payload);
        // Save to temp table
        SchemeTabFieldTemp::updateOrCreate(
            [
                'scheme_id' => $this->schemeId,
                'tab_code'  => $this->activeTabCode,
            ],
            [
                'field_ids' => $payload,
            ]
        );

        $this->tabFields[$this->activeTabCode] = [];
        // $this->tabFields[$this->activeTabCode] = [];
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
    // public function removeField($tabCode, $fieldId)
    // {
    //     if ($this->isFieldMandatory($fieldId)) {
    //         return;
    //     }
    //     unset($this->tabFields[$tabCode][$fieldId]);

    //     if (empty($this->tabFields[$tabCode])) {
    //         unset($this->tabFields[$tabCode]);
    //     }
    // }

    public function removeField($tabCode, $fieldId)
    {
        if ($this->isFieldMandatory($fieldId)) {
            return;
        }
        unset($this->tabFields[$tabCode][$fieldId]);
        if (empty($this->tabFields[$tabCode])) {
            unset($this->tabFields[$tabCode]);
        }
        $temp = SchemeTabFieldTemp::where('scheme_id', $this->schemeId)
            ->where('tab_code', $tabCode)
            ->first();
        if (!$temp || !is_array($temp->field_ids)) {
            return;
        }
        $filtered = collect($temp->field_ids)
            ->reject(fn($f) => (int)$f['field_id'] === (int)$fieldId)
            ->values()
            ->map(fn($f, $i) => [
                'field_id' => $f['field_id'],
                'position' => $i + 1,
            ])
            ->toArray();
        $temp->update([
            'field_ids' => $filtered,
        ]);
    }
    /* -----------------------------
     | Preview
     |-----------------------------*/
    public function openPreview($tabCode)
    {
        $this->activeTabCode = $tabCode;

        $tab = collect($this->tabs)
            ->firstWhere('tab_code', $tabCode);
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
    // public function updateFieldOrder($tabCode, $orderedIds)
    // {
    //     if (!isset($this->tabFields[$tabCode])) return;
    //     $newOrder = [];
    //     foreach ($orderedIds as $fid) {
    //         if (isset($this->tabFields[$tabCode][$fid])) {
    //             $newOrder[$fid] = $this->tabFields[$tabCode][$fid];
    //         }
    //     }
    //     $this->tabFields[$tabCode] = $newOrder;
    // }
    public function updateFieldOrder($tabCode, $orderedIds)
    {
        if (!isset($this->tabFields[$tabCode])) return;

        // Update in-memory
        $newOrder = [];
        foreach ($orderedIds as $fid) {
            if (isset($this->tabFields[$tabCode][$fid])) {
                $newOrder[$fid] = $this->tabFields[$tabCode][$fid];
            }
        }
        $this->tabFields[$tabCode] = $newOrder;

        // Update temp table
        $payload = [];
        foreach ($orderedIds as $i => $fid) {
            $payload[] = [
                'field_id' => (int) $fid,
                'position' => $i + 1,
            ];
        }
        SchemeTabFieldTemp::updateOrCreate(
            [
                'scheme_id' => $this->schemeId,
                'tab_code'  => $tabCode,
            ],
            [
                'field_ids' => $payload,
            ]
        );
    }


    public function getPreviewFieldsProperty()
    {
        // dd('vghbh');
        // dd($this->activeTabCode);
        if (!$this->activeTabCode) {
            return collect();
        }

        $fieldIds = array_keys($this->tabFields[$this->activeTabCode] ?? []);
        // dd($fieldIds);
        if (empty($fieldIds)) {
            return collect();
        }
        if ($this->activeTabCode == 104) {
            // dd('bgbjk');
            return Codemaster::where('id', $fieldIds)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get()
                // dd($this->modalFields);
                ->sortBy(fn($f) => array_search($f->id, $fieldIds))
                ->values();
            // dd();
        } else {

            return SchemeTabBasefield::whereIn('id', $fieldIds)
                ->whereIn('scheme_id', [0, $this->schemeId])
                ->whereIn('tab_code', [0, $this->activeTabCode])
                ->where('is_active', true)
                ->get()
                ->sortBy(fn($f) => array_search($f->id, $fieldIds))
                ->values();
        }
    }
    public function render()
    {
        return view('livewire.scheme-tab-field-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
