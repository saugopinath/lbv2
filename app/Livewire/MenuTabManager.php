<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use App\Models\SchemeTabMapping;
use App\Models\TabFormField;
use Illuminate\Support\Collection;

class MenuTabManager extends Component
{
    public $selectedSchemeId;
    public Collection $assignedTabs;
    public $fieldsByTab = [];

    public $showModal = false;         // Add/Edit modal
    public $showPreview = false;       // Preview modal
    public $previewTabCode = null;     // Current tab for preview

    public $currentTabCode;
    public $editingFieldId = null;

    public $form = [
        'level_name' => '',
        'field_name' => '',
        'field_id' => '',
        'field_type' => '',
        'options' => '',
        'validation_rule' => '',
        'regex' => '',
        'is_active' => true,
    ];

    public function mount()
    {
        $this->assignedTabs = collect();
        $this->fieldsByTab = [];
    }

    public function loadAssignedTabs()
    {
        $this->fieldsByTab = [];

        if ($this->selectedSchemeId) {
            $this->assignedTabs = SchemeTabMapping::where('scheme_id', $this->selectedSchemeId)
                ->with('masterTab')
                ->orderBy('position')
                ->get();

            foreach ($this->assignedTabs as $mapping) {
                $this->fieldsByTab[$mapping->tab_code] = TabFormField::where('tab_code', $mapping->tab_code)
                    ->where(function ($query) {
                        $query->where('is_common', true)
                              ->orWhere('scheme_id', $this->selectedSchemeId);
                    })
                    ->orderBy('field_position')
                    ->get();
            }
        } else {
            $this->assignedTabs = collect();
        }
    }

    // --- Add/Edit Modal ---
    public function openAddModal($tabCode)
    {
        $this->currentTabCode = $tabCode;
        $this->editingFieldId = null;
        $this->reset('form');
        $this->showModal = true;
    }

    public function openEditModal($tabCode, $fieldId)
    {
        $this->currentTabCode = $tabCode;
        $this->editingFieldId = $fieldId;

        $field = TabFormField::findOrFail($fieldId);

        $this->form = $field->only([
            'level_name', 'field_name', 'field_id', 'field_type',
            'options', 'validation_rule', 'regex', 'is_active'
        ]);

        $this->showModal = true;
    }

    public function saveField()
    {
        $this->validate([
            'form.field_name' => 'required|string|max:100',
            'form.field_id' => 'required|string|max:50|' .
                ($this->editingFieldId ? "unique:tab_form_fields,field_id,{$this->editingFieldId}" : 'unique:tab_form_fields,field_id'),
            'form.field_type' => 'required|in:text,number,email,date,select,checkbox,textarea,file',
            'form.options' => 'nullable|string',
            'form.validation_rule' => 'nullable|string',
            'form.regex' => 'nullable|string',
        ]);

        $data = $this->form;

        if ($this->editingFieldId) {
            TabFormField::findOrFail($this->editingFieldId)->update($data);
            $message = 'Field updated successfully!';
        } else {
            $maxPosition = TabFormField::where('tab_code', $this->currentTabCode)
                ->where('scheme_id', $this->selectedSchemeId)
                ->max('field_position') ?? 0;

            TabFormField::create([
                'is_common' => false,
                'tab_code' => $this->currentTabCode,
                'scheme_id' => $this->selectedSchemeId,
                'field_position' => $maxPosition + 1,
            ] + $data);

            $message = 'Field added successfully!';
        }

        $this->reset(['form', 'editingFieldId']);
        $this->showModal = false;
        $this->loadAssignedTabs();

        session()->flash('message', $message);
    }

    // --- Delete Field ---
    public function deleteField($fieldId)
    {
        $field = TabFormField::findOrFail($fieldId);

        if ($field->is_common) {
            session()->flash('error', 'Cannot delete common fields.');
            return;
        }

        $mapping = SchemeTabMapping::where('scheme_id', $this->selectedSchemeId)
            ->where('tab_code', $field->tab_code)
            ->first();

        if ($mapping?->is_finally_submitted) {
            session()->flash('error', 'Cannot delete after final submission.');
            return;
        }

        $field->delete();
        $this->loadAssignedTabs();
        session()->flash('message', 'Field deleted successfully!');
    }

    // --- Final Submit ---
    public function finalSubmit($mappingId)
    {
        SchemeTabMapping::findOrFail($mappingId)->update(['is_finally_submitted' => true]);
        $this->loadAssignedTabs();
        session()->flash('message', 'Tab finalized successfully!');
    }

    // --- Preview Modal ---
    public function openPreview($tabCode)
    {
        $this->previewTabCode = $tabCode;
        $this->showPreview = true;
    }

    public function closePreview()
    {
        $this->showPreview = false;
        $this->previewTabCode = null;
    }

    public function render()
    {
        $schemes = Scheme::where('is_active', 1)->get();

        return view('livewire.menu-tab-manager', [
            'schemes' => $schemes,
        ]);
    }
}
