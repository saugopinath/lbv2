<?php

namespace App\Livewire\CreateAssignOtherFormField;


use Livewire\Component;
use App\Models\Scheme;
use App\Models\FromFieldType;
use App\Models\ValidationRule;
use App\Models\FromFieldAttribute;
use App\Models\MasterSection;
use Illuminate\Support\Facades\Storage;

class CreateOtherfromAttribute extends Component
{
    public $scheme_id;
    public $level_name;
    public $field_id;
    public $field_name;
    public $field_type;
    public array $validation_rule = [];
    public array $options = [];
    public string $option_input = '';

    public string $is_under_section = 'no';
    public string $is_multiple = 'no';

    public $section_id = null;
    public $sections = [];
    public $schemes = [];
    public $fieldTypes = [];
    public $validationRules = [];
    public array $validationRuleOptions = [];
    public string $is_choose_default = 'no';
    public $default_values;
    public $default_value;
    public $defaultOptions;
    public $isdependent = 'no';
    public $depenentOptions;
    public $depenent_on;
    public $isdepenentsec = false;
    public function mount()
    {
        $this->schemes = Scheme::all();
        $this->fieldTypes = FromFieldType::all();
        $this->validationRuleOptions = ValidationRule::all()
            ->map(fn($rule) => [
                'value' => $rule->rule,
                'label' => $rule->description,
            ])
            ->toArray();
        if (FromFieldAttribute::exists()) {
            $this->isdepenentsec = true;
        }
    }
    public function updatedSchemeId()
    {
        $this->resetSection();
    }
    public function updatedIsUnderSection($value)
    {
        if ($value === 'yes' && $this->scheme_id) {
            $this->loadSections();
        } else {
            $this->resetSection();
        }
    }
    public function updatedFieldType($value)
    {
        if ($value !== 'select') {
            $this->is_multiple = 'no';
            $this->is_choose_default = 'no';
        }
        $this->isdependent = 'no';
    }

    protected function loadSections()
    {
        $this->sections = MasterSection::where('scheme_id', $this->scheme_id)
            ->orderBy('section_name')
            ->get();
    }
    protected function resetSection()
    {
        $this->sections = [];
        $this->section_id = null;
    }

    public function updatedIsChooseDefault($value)
    {
        $this->is_choose_default = $value;
        $this->default_values = json_decode(
            Storage::get('form-options.json'),
            true
        );
        $this->default_value = null;
    }
    public function updatedDefaultValue($value)
    {
        $this->defaultOptions = [];
        if ($this->is_choose_default === 'yes') {
            if (isset($this->default_values[$value])) {
                $this->defaultOptions = $this->default_values[$value];
            }
        }
    }
    public function updatedIsdependent($value)
    {
        $this->isdependent = $value;
        $this->depenentOptions = FromFieldAttribute::whereNull('dependent_on')->get();
        $this->depenent_on = null;
    }
    public function updatedDepenentOn($value)
    {
        $this->depenent_on = $value;
    }
    protected function rules()
    {
        return [
            'scheme_id' => 'required',
            'level_name' => 'required|string|max:100',
            'field_id' => 'required|string|max:100',
            'field_name' => 'required|string|max:150',
            'field_type' => 'required|string',
            'validation_rule' => 'required|array|min:1',
            'is_under_section' => 'required|in:yes,no',
            'section_id' => 'required_if:is_under_section,yes',
            'is_multiple' => 'required_if:field_type,select',
            'is_choose_default' => 'required_if:field_type,select',
            'default_value' => 'required_if:is_choose_default,yes',
            'isdependent' => 'required',
            'depenent_on' => 'required_if:isdependent,yes'
        ];
    }
    public function addOption()
    {
        if ($this->option_input !== '') {
            $this->options[] = $this->option_input;
            $this->option_input = '';
        }
    }
    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }
    public function save()
    {
        $this->validate();
        $validationRules = collect($this->validation_rule)
            ->flatten()
            ->filter(fn($v) => is_string($v))
            ->values()
            ->toArray();

        $options = collect($this->options)
            ->flatten()
            ->filter(fn($v) => is_string($v))
            ->values()
            ->toArray();

        FromFieldAttribute::create([
            'scheme_id' => $this->scheme_id,
            'level_name' => $this->level_name,
            'field_id' => $this->field_id,
            'field_label' => $this->field_name,
            'field_type' => $this->field_type,

            'validation_rule' => implode('|', $validationRules),

            'options' => in_array($this->field_type, ['select', 'checkbox', 'radio'])
                ? (
                    $this->is_choose_default === 'yes'
                    ? $this->defaultOptions
                    : $options
                )
                : null,
            'section_id' => $this->is_under_section === 'yes'
                ? $this->section_id
                : null,
            'is_multiple' => $this->field_type === 'select'
                ? ($this->is_multiple === 'yes')
                : false,
            'dependent_on' => $this->isdependent === 'yes' ? $this->depenent_on : null,
        ]);
        $this->reset([
            'level_name',
            'field_id',
            'field_name',
            'field_type',
            'validation_rule',
            'options',
            'option_input',
            'is_under_section',
            'section_id',
            'is_multiple',
            'is_choose_default',
            'default_value',
            'defaultOptions',
            'isdependent',
            'depenent_on'
        ]);

        session()->flash('success', 'Field created successfully');
    }
    public function render()
    {
        return view('livewire.create-assign-other-form-field.create-otherfrom-attribute');
    }
}
