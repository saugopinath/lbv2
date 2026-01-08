<?php

namespace App\Livewire\DynamicForm;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\FromFieldAttribute;
use App\Models\MasterSection;
use App\Models\OtherDetails;
use Illuminate\Support\Facades\Auth;

class RenderDynamicForm extends Component
{
    use WithFileUploads;

    public $schemeId;
    public $application_id;
    public array $fields = [];
    public array $sections = [];
    public array $formData = [];

    public function mount($schemeId, $application_id = null)
    {
        $this->schemeId = $schemeId;
        $this->application_id = $application_id;

        $allFields = FromFieldAttribute::where('scheme_id', $this->schemeId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->fields = $allFields->groupBy('section_id')->toArray();
        $this->sections = MasterSection::where('scheme_id', $this->schemeId)
            ->get()->keyBy('id')->toArray();

        foreach ($allFields as $field) {
            // Initialize based on type
            if (in_array($field->field_type, ['checkbox', 'multiselect'])) {
                $this->formData[$field->field_label] = [];
            } else {
                $this->formData[$field->field_label] = null;
            }
        }

        if ($this->application_id) {
            $existing = OtherDetails::where('application_id', $this->application_id)->first();
            if ($existing && is_array($existing->details)) {
                $this->formData = array_merge($this->formData, $existing->details);
            }
        }
    }

    protected function rules()
    {
        $rules = [];
        foreach (collect($this->fields)->flatten(1) as $field) {
            if ($this->shouldShowField($field)) {
                $rule = $field['validation_rule'];
                if ($rule && $rule !== 'nullable') {
                    $rules["formData.{$field['field_label']}"] = $rule;
                }
            }
        }
        return $rules;
    }
    protected function validationAttributes(): array
    {
        $attributes = [];
        foreach (collect($this->fields)->flatten(1) as $field) {
            $attributes["formData.{$field['field_label']}"] = $field['level_name'];
        }
        return $attributes;
    }
    public function getFieldOptions($field)
    {
        if (!empty($field['options'])) {
            return is_array($field['options']) ? $field['options'] : json_decode($field['options'], true);
        }

        $distCode = $this->formData['district'] ?? null;
        $ruralUrban = $this->formData['rural/urban'] ?? null;

        switch ($field['field_class']) {
            case 'district':
                return \App\Models\District::pluck('name', 'lgd_code')->toArray();
            case 'block':
                if (!$distCode || !$ruralUrban) return [];
                if ($ruralUrban == 1) {
                    return \App\Models\Block::where('district_id', $distCode)->pluck('name', 'lgd_code')->toArray();
                } else if ($ruralUrban == 2) {
                    $subdivisionCodes = \App\Models\Subdivision::where('district_id', $distCode)->pluck('ref_code');
                    return \App\Models\Municipality::whereIn('subdivision_id', $subdivisionCodes)
                        ->pluck('name', 'lgd_code')->toArray();
                }
            case 'panchayat':
                $blockCode = $this->formData['block'] ?? null;
                if (!$blockCode) return [];
                if ($ruralUrban == 1) {
                    return \App\Models\Panchayat::where('block_id', $blockCode)
                        ->pluck('name', 'lgd_code')->toArray();
                } else {
                    return \App\Models\Ward::where('municipality_id', $blockCode)
                        ->pluck('name', 'lgd_code')->toArray();
                }
            default:
                return [];
        }
    }

    public function updatedFormData($value, $key)
    {
        if ($key === 'district') {
            $this->formData['block'] = null;
            $this->formData['panchayat'] = null;
            $this->formData['rural/urban'] = null;
        }
        if ($key === 'rural/urban' || $key === 'block') {
            if ($key === 'rural/urban') $this->formData['block'] = null;
            $this->formData['panchayat'] = null;
        }
    }

    public function shouldShowField($field)
    {
        if (empty($field['dependent_on'])) return true;
        $allFields = collect($this->fields)->flatten(1);
        $parentField = $allFields->firstWhere('id', $field['dependent_on']);
        $parentLabel = $parentField['field_label'] ?? '';
        $parentValue = $this->formData[$parentLabel] ?? null;

        if (!empty($field['dependent_on_values'])) {
            $allowed = is_array($field['dependent_on_values']) ? $field['dependent_on_values'] : json_decode($field['dependent_on_values'], true);
            return in_array((string)$parentValue, array_map('strval', $allowed));
        }
        return !empty($parentValue);
    }

    public function save()
    {
        $this->validate();
        OtherDetails::updateOrCreate(
            ['application_id' => $this->application_id, 'scheme_id' => $this->schemeId],
            ['details' => $this->formData]
        );
        $this->dispatch('toastr', ['type' => 'success', 'message' => 'Saved Successfully!']);
    }

    public function render()
    {
        return view('livewire.dynamic-form.render-dynamic-form');
    }
}
