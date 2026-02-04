<?php

namespace App\Livewire;

use App\Helpers\SchemewiseStoreDataJsonHelper;
use App\Models\Codemaster;
use App\Models\SchemeAttachedDocMappings;
use App\Models\SectionLevelMaster;
use Livewire\Component;
use App\Models\Scheme;
use App\Models\SchemeFinalSubmitCheck;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeTabFieldTemp;
use App\Models\SchemeTabFormField;
use App\Models\SelfDeclerationBasefield;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

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
    public $showEditSelfDeclModal = false;
    public $editingSelfDeclId = null;
    public $editingLevelName = '';
    public $isFinalSubmitted = false;

    protected $listeners = [
        'openSectionLevelModal' => 'open',
    ];


    public bool $showLayoutModal = false;
    public string $layoutMode = '1'; // 1 | 2 | 3 | custom
    public array $rowConfig = [];    // [2,3,1]
    public int $totalFields = 0;
    public int $remainingFixFields = 0;
    public $showDigitalPreview = false;
    public $PreviewFields = [];
    public $digitalPreviewFields = [];

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
            $this->syncFinalSubmitStatus();
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
        $this->syncFinalSubmitStatus();
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
        if (!$this->schemeId) {
            return;
        }
        $fields = SchemeTabFormField::where('scheme_id', $this->schemeId)
            ->where('is_active', true)
            ->orderBy('tab_code')
            ->orderBy('field_position')
            ->get();
        $this->tabFields = [];
        foreach ($fields as $field) {
            $this->tabFields[$field->tab_code][$field->tab_field_id] = $field->level_name;
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
            ->max('field_position');
        $nextPosition = ($lastPosition ?? 0) + 1;
        SchemeAttachedDocMappings::updateOrCreate(
            [
                'scheme_id'   => $this->schemeId,
                'doc_type_id' => $this->selectedDocType,
                'tab_code' => $this->activeTabCode,
                'field_position'       => $nextPosition,
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
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission completed. You cannot modify documents.',
            ]);
            return;
        }
        SchemeAttachedDocMappings::where('id', $id)
            ->where('scheme_id', $this->schemeId)
            ->delete();
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Document removed successfully',
        ]);
        $this->loadAttachedDocuments();
    }
    public function updateDocumentOrder($orderedIds)
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission completed. You cannot modify documents.',
            ]);
            return;
        }
        foreach ($orderedIds as $index => $id) {
            SchemeAttachedDocMappings::where('id', $id)
                ->update(['field_position' => $index + 1]);
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
            ->orderBy('field_position')
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

        $savedIds = SchemeTabFormField::where('scheme_id', $this->schemeId)
            ->where('tab_code', $tabCode)
            ->where('is_active', true)
            ->orderBy('field_position')
            ->pluck('tab_field_id')
            ->toArray();
        // dd($savedIds);

        $existing = !empty($savedIds)
            ? $savedIds
            : array_keys($this->tabFields[$tabCode] ?? []);

        $this->modalSelected = array_values(
            array_unique(array_merge($existing, $mandatoryIds))
        );
        // dd($this->modalSelected);
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
        DB::transaction(function () {
            $schemeId = $this->schemeId;
            $tabCode  = $this->activeTabCode;
            $existingFormFields = SchemeTabFormField::where('scheme_id', $schemeId)
                ->where('tab_code', $tabCode)
                ->where('is_active', true)
                ->pluck('tab_field_id') // IMPORTANT: base field_id
                ->toArray();
            $selectedBaseIds = array_map('intval', $this->modalSelected);
            // dd($selectedBaseIds);
            $toDeactivate = array_diff($existingFormFields, $selectedBaseIds);
            if (!empty($toDeactivate)) {
                SchemeTabFormField::where('scheme_id', $schemeId)
                    ->where('tab_code', $tabCode)
                    ->whereIn('tab_field_id', $toDeactivate)
                    ->delete();
            }
            foreach ($selectedBaseIds as $index => $baseFieldId) {
                // dd($baseFieldId);
                $base = SchemeTabBasefield::findOrFail($baseFieldId);
                if (!$base) {
                    continue;
                }
                // dd($baseFieldId);
                SchemeTabFormField::updateOrCreate(
                    [
                        'tab_field_id' => $baseFieldId,
                        'scheme_id' => $schemeId,
                        'tab_code'  => $tabCode,
                    ],
                    [
                        'level_name'      => $base->level_name,
                        'field_name'      => $base->field_name,
                        'field_type'      => $base->field_type,
                        'field_id'        => $base->field_id,
                        'options'         => $base->options,
                        'validation_rule' => $base->validation_rule,
                        'regex'           => $base->regex,
                        'section_level_id'      => $base->section_level_id,
                        'section_level_type'      => $base->section_level_type,
                        'confirm_of'      => $base->confirm_of,
                        'dependent_on'      => $base->dependent_on,
                        'dependent_on_values'      => $base->dependent_on_values,
                        'field_class'      => $base->field_class,
                        'is_multiple'     => $base->is_multiple,
                        'field_position'  => $index + 1,
                        'is_common'       => $base->is_common,
                        'db_column'       => $base->db_colunm,
                        'is_mandatory'    => $base->is_mendetory,
                        'is_active'       => true,
                        'is_readonly'       => $base->is_readonly,
                    ]
                );
            }
            $this->tabFields[$tabCode] = SchemeTabFormField::where('scheme_id', $schemeId)
                ->where('tab_code', $tabCode)
                ->where('is_active', true)
                ->orderBy('field_position')
                ->pluck('level_name', 'tab_field_id')
                ->toArray();
        });

        $this->closeManageModal();
        $this->syncLayoutAfterFieldChange();


        $this->dispatch('toastr', [
            'type'    => 'success',
            'message' => 'Fields saved successfully',
        ]);
    }


    public function removeField($tabCode, $fieldId)
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission completed. You cannot modify documents.',
            ]);
            return;
        }
        if ($this->isFieldMandatory($fieldId)) {
            return;
        }
        DB::transaction(function () use ($tabCode, $fieldId) {
            SchemeTabFormField::where('tab_field_id', $fieldId)
                ->where('scheme_id', $this->schemeId)
                ->where('tab_code', $tabCode)
                ->delete();
            // Reorder remaining fields
            $remainingFields = SchemeTabFormField::where('scheme_id', $this->schemeId)
                ->where('tab_code', $tabCode)
                ->where('is_active', true)
                ->orderBy('field_position')
                ->get();
            foreach ($remainingFields as $index => $field) {
                $field->update(['field_position' => $index + 1]);
            }
            $this->tabFields[$tabCode] = $remainingFields->pluck('level_name', 'tab_field_id')->toArray();
        });
        $this->syncLayoutAfterFieldChange();
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

        if (!$this->previewTabCode) {
            $this->PreviewFields = collect();
            return;
        }
        $fieldIds = array_keys($this->tabFields[$this->previewTabCode] ?? []);
        if (empty($fieldIds)) {
            $this->PreviewFields = collect();
            return;
        }
        $this->PreviewFields = SchemeTabBasefield::whereIn('id', $fieldIds)
            ->whereIn('scheme_id', [0, $this->schemeId])
            ->whereIn('tab_code', [0, $this->previewTabCode])
            ->where('is_active', true)
            ->get()
            ->sortBy(fn($f) => array_search($f->id, $fieldIds))
            ->values();
    }
    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->activeTabCode = null;
        $this->previewTabName = '';
    }

    public function updateFieldOrder($tabCode, $orderedIds)
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission completed. You cannot modify documents.',
            ]);
            return;
        }
        if (empty($orderedIds) || !$this->schemeId) {
            return;
        }
        DB::transaction(function () use ($tabCode, $orderedIds) {
            foreach ($orderedIds as $index => $fieldId) {
                SchemeTabFormField::where('tab_field_id', $fieldId)
                    ->where('scheme_id', $this->schemeId)
                    ->where('tab_code', $tabCode)
                    ->update([
                        'field_position' => $index + 1,
                    ]);
            }
            $fields = SchemeTabFormField::where('scheme_id', $this->schemeId)
                ->where('tab_code', $tabCode)
                ->where('is_active', true)
                ->orderBy('field_position')
                ->get();
            $this->tabFields[$tabCode] = [];
            foreach ($fields as $field) {
                $this->tabFields[$tabCode][$field->tab_field_id] = $field->level_name;
            }
        });
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

    public function removeSelfDeclarationField(int $fieldId): void
    {
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission completed. You cannot modify documents.',
            ]);
            return;
        }
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
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission completed. You cannot modify documents.',
            ]);
            return;
        }
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
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission completed. You cannot modify documents.',
            ]);
            return;
        }
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

    //final submit form
    public function finalSubmit()
    {
        // dd('final submit');
        // dd($this->schemeId);
        if (!$this->schemeId) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Please select a scheme first',
            ]);
            return;
        }
        if ($this->isFinalSubmitted) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission has been completed.',
            ]);
            return;
        }
        $missingFieldNames = SchemewiseStoreDataJsonHelper::checkMandatoryBaseFields($this->schemeId);
        if (!empty($missingFieldNames)) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Mandatory fields missing: ' . implode(', ', $missingFieldNames),
            ]);
            return;
        }
        DB::beginTransaction();
        try {
            $data = SchemewiseStoreDataJsonHelper::generateSchemeJson($this->schemeId);
            $path = SchemewiseStoreDataJsonHelper::storeSchemeJson($this->schemeId, $data);
            $blade = SchemewiseStoreDataJsonHelper::store($this->schemeId, $data['tabs']);
            if (!$path) {
                throw new \Exception('JSON file could not be saved');
            }
            if (!$blade) {
                throw new \Exception('Blade file could not be saved');
            }
            $finalSubmitStatus = SchemeFinalSubmitCheck::updateOrCreate(
                ['scheme_id' => $this->schemeId],
                ['is_final_submitted' => true]
            );
            if ($finalSubmitStatus) {
                $this->isFinalSubmitted = true;
                DB::commit();
                $this->dispatch('toastr', [
                    'type' => 'success',
                    'message' => 'Scheme Form Field final submitted successfully',
                ]);
            } else {
                if (isset($path)) {
                    Storage::disk('local')->delete($path);
                }
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => 'Final submission failed. Please try again.',
                ]);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($path)) {
                Storage::disk('local')->delete($path);
            }
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Final submission failed. Please try again.',
            ]);
        }
    }
    protected function syncFinalSubmitStatus(): void
    {
        $this->isFinalSubmitted = SchemeFinalSubmitCheck::where('scheme_id', $this->schemeId)
            ->where('is_final_submitted', true)
            ->exists();
    }


    // public function openLayoutModal($tabCode)
    // {
    //     $this->activeTabCode = $tabCode;

    //     $this->totalFields = count(
    //         $this->tabFields[$tabCode] ?? []
    //     );

    //     $saved = DB::table('scheme_tab_layouts')
    //         ->where('scheme_id', $this->schemeId)
    //         ->where('tab_code', $tabCode)
    //         ->value('layout_json');

    //     if ($saved) {
    //         $layout = json_decode($saved, true);

    //         $this->layoutMode = 'custom';
    //         $this->rowConfig = array_map(
    //             fn($row) => (int)($row['columns'] ?? 1),
    //             $layout
    //         );
    //     } else {
    //         $this->layoutMode = '1';
    //         $this->buildDefaultLayout();
    //     }

    //     $this->showLayoutModal = true;
    // }
    // private function buildDefaultLayout(): void
    // {
    //     if ($this->layoutMode === 'custom') return;

    //     $perRow = max(1, (int)$this->layoutMode);

    //     $rows = ceil($this->totalFields / $perRow);

    //     $this->rowConfig = array_fill(0, $rows, $perRow);

    //     $used = array_sum($this->rowConfig);
    //     $this->remainingFixFields = $this->totalFields - $used;
    // }
    // public function updatedRowConfig()
    // {
    //     // total selected columns
    //     $used = array_sum($this->rowConfig);

    //     // remaining fields
    //     $remaining = $this->totalFields - $used;

    //     // 🔹 add rows automatically if fields remain
    //     while ($remaining > 0) {
    //         $this->rowConfig[] = min(3, $remaining);
    //         $remaining -= end($this->rowConfig);
    //     }

    //     // 🔹 trim extra rows if overflow
    //     while ($remaining < 0 && count($this->rowConfig) > 0) {
    //         $last = array_pop($this->rowConfig);
    //         $remaining += $last;
    //     }

    //     $this->remainingFixFields = $remaining;
    // }
    // public function updatedLayoutMode()
    // {
    //     if ($this->layoutMode === 'custom') {

    //         // 🟢 Each field in its own row initially
    //         $this->rowConfig = array_fill(0, $this->totalFields, 1);

    //         // 🟢 Nothing remaining
    //         $this->remainingFixFields = 0;

    //         return;
    //     }

    //     // non-custom (1 / 2 / 3)
    //     $this->buildDefaultLayout();
    // }
    // public function applyLayout()
    // {
    //     $layout = [];

    //     foreach ($this->rowConfig as $i => $count) {
    //         $layout[] = [
    //             'row'     => $i + 1,
    //             'columns' => (int)$count,
    //         ];
    //     }

    //     DB::table('scheme_tab_layouts')->updateOrInsert(
    //         [
    //             'scheme_id' => $this->schemeId,
    //             'tab_code'  => $this->activeTabCode,
    //         ],
    //         [
    //             'layout_json' => json_encode($layout),
    //             'updated_at'  => now(),
    //         ]
    //     );

    //     $this->showLayoutModal = false;
    // }



    //new 

    private function rebuildRowConfig(): void
    {
        $rows = [];
        $used = 0;

        // existing rows respect replace kora hobe 
        foreach ($this->rowConfig as $cols) {
            if ($used >= $this->totalFields) {
                break;
            }

            $cols = max(1, min(3, (int) $cols));
            $rows[] = $cols;
            $used += $cols;
        }

        // field remain thakle  → minimum 1 kore row add
        while ($used < $this->totalFields) {
            $rows[] = 1;          //  default = 1
            $used += 1;
        }

        $this->rowConfig = $rows;
        $this->remainingFixFields = max(0, $this->totalFields - $used);
    }

    // private function rebuildRowConfig(): void
    // {
    //     $rows = [];

    //     foreach ($this->rowConfig as $cols) {
    //         $rows[] = max(1, min(3, (int) $cols));
    //     }

    //     if (empty($rows)) {
    //         $rows = [1];
    //     }

    //     $this->rowConfig = array_values($rows);
    // }
    private function visibleRowCount(): int
    {
        $remainingFields = $this->totalFields;

        foreach ($this->rowConfig as $i => $cols) {
            $cols = max(1, min(3, (int) $cols));
            // this row can host fields (even partially)
            $remainingFields -= $cols;
            // if this row has extra capacity OR fields spill over,
            // we MUST show next row
            if ($remainingFields < 0) {
                return $i + 2;
            }

            if ($remainingFields === 0) {
                return $i + 1;
            }
        }

        return count($this->rowConfig);
    }


    public function openLayoutModal($tabCode)
    {
        $this->activeTabCode = $tabCode;

        $this->totalFields = count(
            $this->tabFields[$tabCode] ?? []
        );

        $saved = DB::table('scheme_tab_layouts')
            ->where('scheme_id', $this->schemeId)
            ->where('tab_code', $tabCode)
            ->value('layout_json');

        if ($saved) {
            // DB layout load
            $layout = json_decode($saved, true);

            $this->layoutMode = 'custom';

            $this->rowConfig = array_map(
                fn($row) => max(1, min(3, (int) ($row['columns'] ?? 1))),
                $layout
            );

            //FIELD COUNT wise row ensure
            $this->rebuildRowConfig();
        } else {
            // 🔹 First time custom
            $this->layoutMode = 'custom';

            // field count = row count, each row = 1
            $this->rowConfig = array_fill(0, $this->totalFields, 1);
            $this->remainingFixFields = 0;
        }

        $this->showLayoutModal = true;
    }


    private function buildDefaultLayout(): void
    {
        if ($this->layoutMode === 'custom') {
            return;
        }

        $perRow = max(1, (int) $this->layoutMode);
        $rows = ceil($this->totalFields / $perRow);

        $this->rowConfig = array_fill(0, $rows, $perRow);

        $used = array_sum($this->rowConfig);
        $this->remainingFixFields = $this->totalFields - $used;
    }
    public function updatedRowConfig()
    {
        $this->rebuildRowConfig();
    }

    // public function updatedLayoutMode()
    // {
    //     if ($this->layoutMode === 'custom') {
    //         if (empty($this->rowConfig)) {
    //             $this->rowConfig = array_fill(0, $this->totalFields, 1);
    //         }
    //         $this->rebuildRowConfig();
    //         return;
    //     }

    //     $perRow = max(1, (int) $this->layoutMode);
    //     $rows = ceil($this->totalFields / $perRow);
    //     $this->rowConfig = array_fill(0, $rows, $perRow);
    // }
    public function updatedLayoutMode()
    {
        if ($this->layoutMode === 'custom') {

            // start with minimum
            $this->rowConfig = [1];
            $this->rebuildRowConfig();
            return;
        }
        // non-custom mode
        $this->buildDefaultLayout();
    }

    public function applyLayout()
    {
        $layout = [];

        foreach ($this->rowConfig as $i => $count) {
            $layout[] = [
                'row'     => $i + 1,
                'columns' => (int) $count,
            ];
        }

        DB::table('scheme_tab_layouts')->updateOrInsert(
            [
                'scheme_id' => $this->schemeId,
                'tab_code'  => $this->activeTabCode,
            ],
            [
                'layout_json' => json_encode($layout),
                'updated_at'  => now(),
            ]
        );

        $this->showLayoutModal = false;
    }
    private function syncLayoutAfterFieldChange(): void
    {
        // 1️field count update
        $this->totalFields = count(
            $this->tabFields[$this->activeTabCode] ?? []
        );

        // 2load saved layout
        $saved = DB::table('scheme_tab_layouts')
            ->where('scheme_id', $this->schemeId)
            ->where('tab_code', $this->activeTabCode)
            ->value('layout_json');

        if (!$saved) {
            return;
        }

        $layout = json_decode($saved, true);

        // 3️⃣ restore rowConfig safely
        $rows = [];
        $used = 0;
        foreach ($layout as $row) {
            if ($used >= $this->totalFields) {
                break;
            }
            $cols = max(1, min(3, (int) ($row['columns'] ?? 1)));
            $rows[] = $cols;
            $used += $cols;
        }
        // ensure minimum rows for remaining fields
        while ($used < $this->totalFields) {
            $rows[] = 1;          //  default new row
            $used += 1;
        }

        $this->rowConfig = $rows;

        // save normalized layout back to DB
        DB::table('scheme_tab_layouts')->updateOrInsert(
            [
                'scheme_id' => $this->schemeId,
                'tab_code'  => $this->activeTabCode,
            ],
            [
                'layout_json' => json_encode(
                    collect($rows)->map(fn($c, $i) => [
                        'row'     => $i + 1,
                        'columns' => $c,
                    ])->values()
                ),
                'updated_at' => now(),
            ]
        );
    }

    public function getTabLayout($tabCode)
    {
        return DB::table('scheme_tab_layouts')
            ->where('scheme_id', $this->schemeId)
            ->where('tab_code', $tabCode)
            ->value('layout_json');
    }

    public function openDigitalPreview()
    {
        $this->showDigitalPreview = true;

        $this->loadTabs();
        $this->loadSelfDeclarationFields();

        $this->prepareDigitalPreviewFields();
    }
    public function downloadDigitalPreviewPdf()
    {
        $this->loadTabs();
        $this->loadSelfDeclarationFields();
        $this->prepareDigitalPreviewFields();

        $pdf = Pdf::loadView('pdf.scheme-digital-preview', [
            'scheme' => Scheme::find($this->schemeId),
            'tabs' => $this->tabs,
            'digitalPreviewFields' => $this->digitalPreviewFields,
            'selfDeclarationDisplay' => $this->selfDeclarationDisplay,
            'attachedDocuments' => $this->attachedDocuments,
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'scheme_preview_' . $this->schemeId . '.pdf'
        );
    }
    private function prepareDigitalPreviewFields()
    {
        $this->digitalPreviewFields = [];

        foreach ($this->tabs as $tab) {

            if (in_array($tab->tab_code, [104, 105])) {
                continue;
            }

            $fieldIds = array_keys($this->tabFields[$tab->tab_code] ?? []);

            if (empty($fieldIds)) {
                $this->digitalPreviewFields[$tab->tab_code] = collect();
                continue;
            }

            $ids = implode(',', $fieldIds);

            $this->digitalPreviewFields[$tab->tab_code] =
                SchemeTabBasefield::whereIn('id', $fieldIds)
                ->whereIn('scheme_id', [0, $this->schemeId])
                ->whereIn('tab_code', [0, $tab->tab_code])
                ->where('is_active', true)
                ->orderByRaw("array_position(ARRAY[$ids]::int[], id::int)")
                ->get();
        }
    }
    public function closeDigitalPreview()
    {
        $this->showDigitalPreview = false;
        $this->digitalPreviewFields = [];
    }

    public function render()
    {
        return view('livewire.scheme-tab-field-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
