<?php

namespace App\Livewire;

use App\Models\Codemaster;
use App\Models\SchemeAttachedDocMappings;
use App\Models\SectionLevelMaster;
use Livewire\Component;
use App\Models\Scheme;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeTabFieldTemp;
use App\Models\SelfDeclerationBasefield;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class SchemeTabFieldManager extends Component
{
    public $schemeId, $lockScheme = false, $tabs = [], $tabFields = [], $showManageModal = false,
        $activeTabCode = null, $modalFields = [], $previewTabName = '', $previewTabCode = null,
        $modalSelected = [], $showFinalPreview = false, $showPreviewModal = false,
        $finalActiveTabCode = null;
    public $finalPreviewFields = [];
    public $docTypes = [];
    public $selectedDocType = null;
    public $isRequired = false;
    public $maxFileSize = '500KB';
    public $attachedDocuments = [];
    public $extensionTypes = [];
    public $selfDeclarationFields;
    public $selfDeclarationGrouped = [];
    public $selfDeclarationDisplay = [];
    // Edit modal state
    public $showEditSelfDeclModal = false;
    public $editingSelfDeclId = null;
    public $editingLevelName = '';


    protected $listeners = [
        'openSectionLevelModal' => 'open',
    ];

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
        if ($this->schemeId) {
            $this->loadAttachedDocuments();
            $this->loadSelfDeclarationFields();
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
        $this->hydrateTabFieldsFromTemp();
    }
    private function hydrateTabFieldsFromTemp(): void
    {
        $temps = SchemeTabFieldTemp::where('scheme_id', $this->schemeId)->get();
        foreach ($temps as $temp) {
            $raw = $temp->field_ids;
            if (empty($raw) || !is_array($raw)) {
                continue;
            }
            $fieldIds = collect($raw)->pluck('field_id')->toArray();
            $names = SchemeTabBasefield::whereIn('id', $fieldIds)
                ->pluck('level_name', 'id');
            $ordered = collect($raw)
                ->sortBy('position')
                ->mapWithKeys(fn($row) => [
                    $row['field_id'] => $names[$row['field_id']] ?? 'Unknown'
                ])
                ->toArray();
            $this->tabFields[$temp->tab_code] = $ordered;
        }
    }
    public function saveDocumentMapping()
    {
        $this->validate(
            [
                'selectedDocType' => 'required',
                'maxFileSize' => 'required|in:100KB,500KB',
                'extensionTypes'  => 'required|array|min:1',
            ],
            [
                'selectedDocType.required' => 'Document type is required',
                'maxFileSize.required' => 'Please fill the file size',
                'maxFileSize.in' => 'Max file size must be like 100KB or 500KB',
                'extensionTypes.required'  => 'Select at least one extension',
            ]
        );
        $lastPosition = SchemeAttachedDocMappings::where('scheme_id', $this->schemeId)
            ->where('tab_code', $this->activeTabCode)
            ->max('position');
        $nextPosition = ($lastPosition ?? 0) + 1;
        SchemeAttachedDocMappings::updateOrCreate(
            [
                'scheme_id'   => $this->schemeId,
                'doc_type_id' => $this->selectedDocType,
                'tab_code' => $this->activeTabCode,
                'position'       => $nextPosition,
            ],
            [
                'is_required'    => $this->isRequired,
                'max_file_size'  => $this->maxFileSize,
                'extension_type' => implode(',', $this->extensionTypes),
            ]
        );
        $this->loadAttachedDocuments();
        $this->reset([
            'selectedDocType',
            'isRequired',
            'maxFileSize',
            'extensionTypes',
        ]);
        session()->flash('message', 'Document saved successfully');
    }
    public function updatedMaxFileSize($value)
    {
        if ($value === null || $value === '') {
            return;
        }
        $this->maxFileSize = preg_replace('/[^0-9]/', '', $value) . 'KB';
    }
    public function removeDocument($id)
    {
        SchemeAttachedDocMappings::where('id', $id)
            ->where('scheme_id', $this->schemeId)
            ->delete();
        $this->loadAttachedDocuments();
    }
    public function updateDocumentOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            SchemeAttachedDocMappings::where('id', $id)
                ->update(['position' => $index + 1]);
        }

        $this->loadAttachedDocuments();
    }
    public function setActiveTab($tabCode)
    {
        // dd('dcsf');
        $this->activeTabCode = $tabCode;
        if ($tabCode == 104) {
            $this->loadAttachedDocuments();
        }
        if ($tabCode == 105) {
            $this->loadSelfDeclarationFields();
        }
    }
    public function loadAttachedDocuments()
    {
        $this->attachedDocuments = SchemeAttachedDocMappings::with('docType')
            ->where('scheme_id', $this->schemeId)
            ->where('tab_code', 104)
            ->orderBy('position')
            ->get();
    }
    public function openManageModal($tabCode)
    {
        // dd('fff');
        $this->activeTabCode = $tabCode;
        $this->showManageModal = true;

        if ($tabCode == 104) {
            $this->docTypes = Codemaster::where('parent_id', 16)
                ->orderBy('name')
                ->get();
            $this->loadAttachedDocuments();
            return;
        }
        if ($tabCode == 105) {
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


        $temp = SchemeTabFieldTemp::where('scheme_id', $this->schemeId)
            ->where('tab_code', $tabCode)
            ->first();

        $savedIds = collect($temp?->field_ids ?? [])
            ->sortBy('position')
            ->pluck('field_id')
            ->toArray();


        $existing = !empty($savedIds)
            ? $savedIds
            : array_keys($this->tabFields[$tabCode] ?? []);

        $this->modalSelected = array_values(
            array_unique(array_merge($existing, $mandatoryIds))
        );
    }
    public function isFieldMandatory($fieldId)
    {
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
    public function saveManageFields()
    {
        $payload = [];
        foreach ($this->modalSelected as $index => $fid) {
            $payload[] = [
                'field_id' => (int) $fid,
                'position' => $index + 1,
            ];
        }
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
    public function openPreview($tabCode)
    {
        $this->activeTabCode = $tabCode;
        $tab = collect($this->tabs)
            ->firstWhere('tab_code', $tabCode);
        $this->previewTabName = $tab?->masterTab?->tab_name ?? 'Preview';
        $this->previewTabCode = $tab?->masterTab?->tab_code ?? 'Preview';
        $this->showPreviewModal = true;
        if ($this->activeTabCode == 104) {
            $this->loadAttachedDocuments();
        }
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
    #[On('self-declaration-saved')]
    public function afterSelfDeclarationSaved()
    {
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Self Decleration Field configured successfully!'
        ]);
        $this->closeManageModal();
        $this->loadSelfDeclarationFields();
        $this->loadTabs();
    }
    public function updateSelfDeclarationOrder(array $orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            SelfDeclerationBasefield::where('id', $id)
                ->update([
                    'field_position' => $index + 1
                ]);
        }
        $this->loadSelfDeclarationFields();
    }
    public function removeSelfDeclarationField(int $fieldId): void
    {
        SelfDeclerationBasefield::where('id', $fieldId)
            ->where('scheme_id', $this->schemeId)
            ->delete();
        $fields = SelfDeclerationBasefield::where('scheme_id', $this->schemeId)
            ->where('tab_code', 105)
            ->where('is_active', true)
            ->orderBy('field_position')
            ->get();

        foreach ($fields as $index => $field) {
            $field->update([
                'field_position' => $index + 1
            ]);
        }
        $this->loadSelfDeclarationFields();
    }
    public function editSelfDeclarationField($fieldId)
    {
        $field = SelfDeclerationBasefield::findOrFail($fieldId);

        $this->editingSelfDeclId = $field->id;
        $this->editingLevelName = $field->level_name;
        $this->showEditSelfDeclModal = true;
    }
    public function updateSelfDeclarationField()
    {
        $this->validate([
            'editingLevelName' => 'required|string|max:255',
        ]);

        SelfDeclerationBasefield::where('id', $this->editingSelfDeclId)
            ->update([
                'level_name' => $this->editingLevelName,
            ]);

        // reset modal state
        $this->showEditSelfDeclModal = false;
        $this->editingSelfDeclId = null;
        $this->editingLevelName = '';

        // reload list
        $this->loadSelfDeclarationFields();

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Self Declaration label updated successfully'
        ]);
    }
    public function updateSelfDeclarationOrderAndSection(array $rows)
    {
        DB::transaction(function () use ($rows) {

            foreach ($rows as $index => $row) {

                $sectionType = null;
                $sectionId   = null;

                if (!empty($row['section'])) {
                    [$sectionType, $sectionId] = explode('-', $row['section']);
                }

                SelfDeclerationBasefield::where('id', $row['id'])
                    ->update([
                        'field_position'     => $index + 1,
                        'section_level_type' => $sectionType,
                        'section_level_id'   => $sectionId,
                    ]);
            }
        });

        $this->loadSelfDeclarationFields();
    }
    public function loadSelfDeclarationFields()
    {
        $fields = SelfDeclerationBasefield::where('scheme_id', $this->schemeId)
            ->where('tab_code', 105)
            ->where('is_active', true)
            ->orderBy('field_position')
            ->get()
            ->values();

        $sectionMap = SectionLevelMaster::pluck('section_level_name', 'id')->toArray();

        $result = [];
        $lastKey = null;

        foreach ($fields as $i => $field) {

            $hasSection = !empty($field->section_level_id);

            $currentKey = $hasSection
                ? $field->section_level_type . '-' . $field->section_level_id
                : null;

            $next = $fields[$i + 1] ?? null;

            $nextKey = (!empty($next?->section_level_id))
                ? $next->section_level_type . '-' . $next->section_level_id
                : null;

            $result[] = [
                'field' => $field,
                'show_section_start' => $hasSection && $currentKey !== $lastKey,
                'show_section_end'   => $hasSection && $currentKey !== $nextKey,
                'section_title'      => $hasSection
                    ? ($sectionMap[$field->section_level_id] ?? 'Section')
                    : null,
            ];

            if ($hasSection) {
                $lastKey = $currentKey;
            }
        }

        $this->selfDeclarationDisplay = $result;
    }
    public function render()
    {
        return view('livewire.scheme-tab-field-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
