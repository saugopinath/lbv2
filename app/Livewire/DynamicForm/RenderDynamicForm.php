<?php

namespace App\Livewire\DynamicForm;

use Livewire\Component;
use App\Models\FromFieldAttribute;
use App\Models\MasterSection;
use App\Models\District;
use App\Models\Subdivision;
use App\Models\Municipality;
use App\Models\Block;
use App\Models\Panchayat;
use Illuminate\Support\Collection;

class RenderDynamicForm extends Component
{
    public $schemeId;
    public array $fields = [];
    public array $sections = [];
    public array $formData = [];

    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;

        $allFields = FromFieldAttribute::where('scheme_id', $this->schemeId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->fields = $allFields->groupBy('section_id')->toArray();
        $this->sections = MasterSection::where('scheme_id', $this->schemeId)
            ->get()->keyBy('id')->toArray();

        foreach ($allFields as $field) {
            $this->formData[$field->field_label] = null;
        }
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
                if ($distCode && $ruralUrban) {
                    // ১ = Urban (Municipality)
                    if ($ruralUrban == 1) {
                        return \App\Models\Block::where('district_id', $distCode)->pluck('name', 'lgd_code')->toArray();
                    }
                    // ২ = Rural (Block)
                    else if ($ruralUrban == 2) {
                        $subdivisionCodes = \App\Models\Subdivision::where('district_id', $distCode)->pluck('ref_code');
                        return \App\Models\Municipality::whereIn('subdivision_id', $subdivisionCodes)
                            ->pluck('name', 'lgd_code')->toArray();
                    }
                }
                return [];

            case 'panchayat':
                $blockCode = $this->formData['block'] ?? null;
                if (!$blockCode) return [];

                // যদি আরবান হয় (১), তবে Ward টেবিল থেকে ডেটা আসবে
                if ($ruralUrban == 1) {
                    return \App\Models\Panchayat::where('block_id', $blockCode)
                        ->pluck('name', 'lgd_code')->toArray();
                }
                // যদি রুরাল হয় (২), তবে Panchayat (GP) টেবিল থেকে ডেটা আসবে
                else {

                    return \App\Models\Ward::where('municipality_id', $blockCode)
                        ->pluck('name', 'lgd_code')->toArray();
                }

            default:
                return [];
        }
    }
    /**
     * যখনই formData অ্যারের কোনো ভ্যালু আপডেট হবে, এই ফাংশনটি ট্রিগার হবে
     */
    public function updatedFormData($value, $key)
    {
        // ১. যদি District পরিবর্তন হয়, তবে তার নিচের সব রিসেট হবে
        if ($key === 'district') {
            $this->formData['block'] = null;
            $this->formData['panchayat'] = null;
            $this->formData['rural/urban'] = null;
            // যদি subdivision ইন্টারনালি ব্যবহার করেন, সেটিও রিসেট করতে পারেন
            if (isset($this->formData['subdivision'])) {
                $this->formData['subdivision'] = null;
            }
        }

        // ২. যদি Rural/Urban পরিবর্তন হয়, তবে ব্লক এবং পঞ্চায়েত রিসেট হবে
        if ($key === 'rural/urban') {
            $this->formData['block'] = null;
            $this->formData['panchayat'] = null;
        }

        // ৩. যদি Block/Municipality পরিবর্তন হয়, তবে Panchayat/Ward রিসেট হবে
        if ($key === 'block') {
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
            $allowedValues = is_array($field['dependent_on_values'])
                ? $field['dependent_on_values']
                : json_decode($field['dependent_on_values'], true);

            return in_array((string)$parentValue, array_map('strval', $allowedValues));
        }

        return !empty($parentValue);
    }

    public function render()
    {
        return view('livewire.dynamic-form.render-dynamic-form');
    }
}
