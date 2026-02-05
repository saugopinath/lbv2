<?php

namespace App\Livewire;

use App\Models\FromFieldType;
use Livewire\Component;
use App\Models\Scheme;
use App\Models\MasterTab;
use App\Models\MasterSectionLevel;
use App\Models\SchemeTabMapping;
use App\Models\SectionLevelMaster;
use App\Models\SelfDeclerationBasefield;
use App\Models\ValidationRule;
use Illuminate\Support\Str;

class AddSelfDeclerationField extends Component
{
    public $schemes;
    public $tabs = [];
    public $scheme_id;
    public $tab_code;
    public $level_name;
    public $field_name;
    public $field_id;
    public $field_type;
    public $is_under_section = 'no'; // yes / no
    public string $is_multiple = 'no';
    public $section_level_type; // 0 = section, 1 = level
    public $section_id;
    public $sections = [];
    public $fieldTypes = [];
    public $validationRuleOptions = [];
    public array $options = [];
    public string $option_input = '';
    public array $validation_rule = [];
    public $show_multiple;
    public $isContextLocked = false;
    protected $listeners = [
        'submit-self-declaration' => 'save',
    ];
    // public function mount()
    // {
    //     $this->schemes = Scheme::where('is_active', true)->get();
    //     $this->fieldTypes = FromFieldType::all();
    //     $this->validationRuleOptions = ValidationRule::all()
    //         ->map(fn($rule) => [
    //             'value' => $rule->rule,
    //             'label' => $rule->description,
    //         ])
    //         ->toArray();
    // }
    public function mount($scheme_id = null, $tab_code = null)
    {
        $this->schemes = Scheme::where('is_active', true)->get();
        $this->fieldTypes = FromFieldType::all();
        $this->validationRuleOptions = ValidationRule::all()
            ->pluck('description', 'rule')
            ->toArray();

        if ($scheme_id && $tab_code) {
            $this->scheme_id = $scheme_id;
            $this->tab_code = $tab_code;
            $this->isContextLocked = true;

            $this->tabs = SchemeTabMapping::with('masterTab')
                ->where('scheme_id', $scheme_id)
                ->where('tab_code', $tab_code)
                ->where('is_active', true)
                ->get();
        }
    }


    public function updatedSchemeId()
    {
        if ($this->isContextLocked) {
            return;
        }
        $this->tabs = SchemeTabMapping::with('masterTab')
            ->where('scheme_id', $this->scheme_id)
            ->where('is_active', true)
            ->whereHas(
                'masterTab',
                fn($q) =>
                $q->where('tab_code', '105')
            )
            ->get();
        // dd($this->tabs);
        $this->reset([
            'tab_code',
            'options',
            'option_input',
            'is_multiple',
            'validation_rule',
            'level_name',
            'field_name',
            'field_id',
            'is_under_section',
            'field_type',
            'section_level_type',
            'section_id',
            'sections'
        ]);
    }

    public function updatedTabCode()
    {
        $this->reset([
            'validation_rule',
            'level_name',
            'field_name',
            'field_id',
            'is_under_section',
            'field_type',
            'section_level_type',
            'section_id',
            'sections',
            'options',
            'option_input',
            'is_multiple'
        ]);
    }
    public function updatedIsUnderSection()
    {
        $this->reset(['section_level_type', 'section_id', 'sections']);
    }
    public function updatedSectionLevelType()
    {
        if ($this->is_under_section !== 'yes') {
            $this->sections = [];
            return;
        }
        $this->sections = SectionLevelMaster::where('is_active', true)->where('section_level_code', $this->section_level_type)->get();
    }
    public function updatedFieldType()
    {
        if ($this->field_type === 'select') {
            $this->show_multiple = true;
        } else {
            $this->show_multiple = false;
        }
        $this->reset([
            'options',
            'option_input',
            'is_multiple',
            'validation_rule',
            'level_name',
            'field_name',
            'field_id',
        ]);
    }
    public function updatedFieldName($value)
    {
        
        $this->field_id = Str::slug($value, '_');
        
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
        // dd($this->scheme_id, $this->tab_code, $this->field_type, $this->field_name, $this->field_id, $this->is_under_section, $this->section_level_type, $this->section_id, $this->is_multiple, $this->options, $this->validation_rule, $this->level_name);
        $this->validate([
            'scheme_id' => 'required',
            'tab_code' => 'required',
            'field_type' => 'required',
            'field_name' => 'required',
            'field_id' => 'required',
            'level_name' => 'required',
            'validation_rule' => 'required',
            'is_multiple' => 'required_if:field_type,select',
            // 'options' => 'required_if:field_type,select',
            'section_level_type' => 'required_if:is_under_section,yes',
            'section_id' => 'required_if:is_under_section,yes',
            'is_under_section' => 'required',
        ]);
        $lastPosition = SelfDeclerationBasefield::where('scheme_id', $this->scheme_id)
            ->where('tab_code', $this->tab_code)
            ->max('field_position');
            
        $this->options = collect($this->options ?? [])
            ->flatten()
            ->filter(fn($v) => is_string($v) && $v !== '')
            ->values()
            ->mapWithKeys(fn($value, $index) => [$index + 1 => $value])
            ->toArray();

        $nextPosition = ($lastPosition ?? 0) + 1;
        SelfDeclerationBasefield::create([
            'scheme_id' => $this->scheme_id,
            'tab_code' => $this->tab_code,
            'field_name' => $this->field_name,
            'field_id' => $this->field_id,
            'level_name' => $this->level_name,
            'field_type' => $this->field_type,
            'section_level_id' => $this->section_id,
            'section_level_type' => $this->section_level_type,
            'is_multiple' => $this->is_multiple,
            'options' => $this->options,
            'validation_rule' => empty($this->validation_rule)
                ? null
                : implode('|', array_unique($this->validation_rule)),

            'field_position' => $nextPosition,
            'is_active' => true,
            'db_column' => 'other_details',
        ]);

        $this->dispatch('self-declaration-saved');
        $this->reset([
            'scheme_id',
            'tab_code',
            'options',
            'option_input',
            'is_multiple',
            'validation_rule',
            'level_name',
            'field_name',
            'field_id',
            'is_under_section',
            'field_type',
            'section_level_type',
            'section_id',
            'sections'
        ]);
    }

    public function render()
    {
        return view('livewire.add-self-declerationfield');
    }
}
