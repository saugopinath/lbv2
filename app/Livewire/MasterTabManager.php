<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\MasterTab;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabFormField;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeAttachedDocMappings;
use App\Models\SelfDeclerationBasefield;
use App\Models\SectionLevelMaster;
use Illuminate\Support\Facades\DB;

class MasterTabManager extends Component
{
    public $selectedSchemeId;
    public $selectedSchemeName;

    public $selectedTabs = [];
    public $positions = [];
    public $selectedTabCode = null;

    // 🔹 Preview
    public $showPreview = false;
    public $previewActiveTabCode = null;
    public $previewFormFields;
    public $attachedDocuments = [];
    public $selfDeclarationDisplay = [];

    public $mappingSaved = false;

    protected $rules = [
        'selectedSchemeId' => 'required|exists:schemes,id',
        'selectedTabs'     => 'required|array|min:1',
    ];

    /* =========================
     | SCHEME CHANGE
     =========================*/
    public function updatedSelectedSchemeId($value)
    {
        if (!$value) {
            $this->reset([
                'selectedSchemeName',
                'selectedTabs',
                'positions',
                'selectedTabCode',
                'mappingSaved',
                'previewFormFields',
                'attachedDocuments',
                'selfDeclarationDisplay',
            ]);
            return;
        }

        $this->mappingSaved = false;
        $this->selectedTabs = [];
        $this->positions = [];

        $scheme = Scheme::find($value);
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

    /* =========================
     | ADD TAB
     =========================*/
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

    /* =========================
     | REMOVE TAB
     =========================*/
    public function removeTab($tabCode)
    {
        $this->selectedTabs = array_values(
            array_diff($this->selectedTabs, [(int)$tabCode])
        );

        $this->recalculatePositions();
        $this->mappingSaved = false;
    }

    /* =========================
     | DRAG ORDER
     =========================*/
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

    /* =========================
     | SAVE
     =========================*/
    public function submit()
    {
        $this->validate();

        DB::transaction(function () {
            SchemeTabMapping::where('scheme_id', $this->selectedSchemeId)
                ->update(['is_active' => false]);

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

    /* =========================
     | PREVIEW
     =========================*/
    public function openPreview()
    {
        $this->showPreview = true;
        $this->previewActiveTabCode = $this->selectedTabs[0] ?? null;

        if ($this->previewActiveTabCode) {
            $this->loadPreviewByTab($this->previewActiveTabCode);
        }
    }

    public function setPreviewTab($tabCode)
    {
        $this->previewActiveTabCode = (int) $tabCode;
        $this->loadPreviewByTab($tabCode);
    }

    private function loadPreviewByTab($tabCode)
    {
        if ($tabCode == 104) {
            $this->loadAttachedDocuments();
            return;
        }

        if ($tabCode == 105) {
            $this->loadSelfDeclarationFields();
            return;
        }

        $this->loadPreviewFields($tabCode);
    }

    private function loadPreviewFields($tabCode)
    {
        $fieldIds = SchemeTabFormField::where('scheme_id', $this->selectedSchemeId)
            ->where('tab_code', $tabCode)
            ->where('is_active', true)
            ->orderBy('field_position')
            ->pluck('tab_field_id')
            ->toArray();

        if (empty($fieldIds)) {
            $this->previewFormFields = collect();
            return;
        }

        $this->previewFormFields = SchemeTabBasefield::whereIn('id', $fieldIds)
            ->whereIn('scheme_id', [0, $this->selectedSchemeId])
            ->whereIn('tab_code', [0, $tabCode])
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($f) => array_search($f->id, $fieldIds))
            ->values();
    }

    private function loadAttachedDocuments()
    {
        $this->attachedDocuments = SchemeAttachedDocMappings::with('docType')
            ->where('scheme_id', $this->selectedSchemeId)
            ->where('tab_code', 104)
            ->orderBy('position')
            ->get();
    }

    private function loadSelfDeclarationFields()
    {
        $fields = SelfDeclerationBasefield::where('scheme_id', $this->selectedSchemeId)
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

    public function closePreview()
    {
        $this->showPreview = false;
        $this->previewActiveTabCode = null;
        $this->previewFormFields = collect();
        $this->attachedDocuments = [];
        $this->selfDeclarationDisplay = [];
    }

    public function render()
    {
        return view('livewire.master-tab-manager', [
            'schemes' => Scheme::where('is_active', true)->get(),
            'allTabs' => MasterTab::where('is_active', true)->orderBy('tab_name')->get(),
        ]);
    }
}
