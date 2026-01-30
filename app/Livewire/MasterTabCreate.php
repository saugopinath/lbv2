<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\MasterTab;
use App\Models\MasterSection;
use App\Models\FromFieldType;
use App\Models\ValidationRule;
use App\Models\SchemeTabBasefield;
use App\Models\SectionLevelMaster;
use App\Services\DynamicModelMigrationService;
use Illuminate\Validation\Rule;

class MasterTabCreate extends Component
{
    /* ---------------- TAB INFO ---------------- */
    public string $tab_name = '';
    public string $tab_short_name = '';
    public string $tab_code = '';
    public string $is_append_multiple = 'no';

    public string $model_name = '';
    public string $table_name = '';
    public ?int $scheme_id = null;

    /* ---------------- FIELD LIST ---------------- */
    public array $fields = [];

    /* ---------------- MODAL STATE ---------------- */
    public bool $showModal = false;
    public ?int $editIndex = null;

    /* ---------------- COLUMN INFO ---------------- */
    public ?string $column_name = null;
    public ?string $db_column = null;
    public string $column_type = '';
    public ?int $length = null;
    public string $nullable = 'no';
    public string $default_enabled = 'no';
    public string $mendetory = 'no';
    public ?string $default_value = null;
    public ?string $db_default_value = null;
    public ?string $constant_value = null;

    public string $key_type = 'none';
    public ?string $key_name = null;
    public ?string $key_reference = null;

    /* ---------------- FORM FIELD INFO ---------------- */
    public ?string $level_name = null;
    public ?string $field_id = null;
    public ?string $field_name = null;
    public string $field_type = '';
    public ?string $view_type = null;
    public ?string $field_class = null;

    public array $validation_rule = [];

    /* ---------------- SECTION / DEFAULT ---------------- */
    public string $is_under_section = 'no';
    public  $section_id = null;
    public  $sections = [];

    public  $is_choose_default = 'no';
    public  $default_values = [];
    public $default_value_key = null;

    public ?string $fk_table = null;
    public ?string $fk_column = null;
    public array $fkTables = [];
    public array $fkColumns = [];

    /* ---------------- MULTI / DEPENDENCY ---------------- */
    public string $is_multiple = 'no';

    public string $isconfirm = 'no';
    public $confirm_of = null;
    public  $confirmOptions = [];

    public string $isdependent = 'no';
    public $depenent_on = null;
    public  $depenentOptions = [];

    public string $isdependentvalue = 'no';
    public  $depvalues = [];
    public array $depvaluesopt = [];

    public bool $isdepenentsec = false;
    public bool $depvalueradio = false;
    public $fieldTypes = [];
    public array $validationRuleOptions = [];

    public string $option_input = '';
    public array $options = [];
    public array $defaultOptions = [];
    public $field_position = 0;
    public $regex = null;
    public $section_type = 0;

    public function mount(): void
    {
        $this->fieldTypes = FromFieldType::all();
        // dd($this->fieldTypes);
        $this->validationRuleOptions =  ValidationRule::all()
            ->pluck('description', 'rule')
            ->toArray();

        $this->default_values = json_decode(
            file_get_contents(public_path('js/form-options.json')),
            true
        ) ?? [];
        if (SchemeTabBasefield::exists()) {
            $this->isdepenentsec = true;
        }
    }

    public function updatedTabName($value): void
    {
        $this->model_name = Str::studly(Str::singular($value));
        $this->table_name = Str::snake(Str::pluralStudly($value));
        $this->tab_short_name = Str::snake($value);
        $this->tab_code = MasterTab::max('tab_code') + 1;
        if ($this->model_name) {
            $checkModelName = MasterTab::where('tab_model_name', $this->model_name)->first();
            $checkModel = file_exists(app_path('Models/' . $this->model_name . '.php'));
            if ($checkModelName || $checkModel) {
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => 'Model already exists for this Tab. Please choose a different Tab name.',
                ]);
                return;
            }
        }
    }
    public function updatedFieldName($value): void
    {
        $this->field_id = Str::snake($value);
        $this->column_name = Str::snake($value);
        $this->db_column = Str::snake($value);
    }
    public function updatedIsUnderSection($value): void
    {
        if ($value === 'yes') {
            $this->sections = SectionLevelMaster::where('is_active', true)->where('section_level_code', 0)->get();
        } else {
            $this->sections = [];
            $this->section_id = null;
        }
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

    public function updatedFieldType($value): void
    {
        $map = [
            'text'      => 'string',
            'password'  => 'string',
            'number'    => 'integer',
            'date'      => 'date',
            'textarea'  => 'text',
            'select'    => 'integer',
            'radio'     => 'smallInteger',
            'checkbox'  => 'smallInteger',
        ];
        $this->validation_rule = [];
        $this->is_under_section = 'no';
        $this->is_choose_default = 'no';
        $this->is_multiple = 'no';
        $this->isconfirm = 'no';
        $this->isdependent = 'no';
        $this->isdependentvalue = 'no';
        $this->depenent_on = null;
        $this->depvalues = [];
        $this->depvaluesopt = [];
        $this->depvalueradio = false;
        $this->confirmOptions = [];
        $this->depenentOptions = [];
        $this->options = [];
        $this->defaultOptions = [];
        $this->field_position = 0;
        $this->regex = null;
        $this->section_type = 0;
        $this->section_id = null;
        $this->default_value = null;
        $this->confirm_of = null;
        if ($value === 'select' && $this->is_multiple === 'yes') {
            $this->column_type = 'jsonb';
        } else {
            $this->column_type = $map[$value] ?? 'string';
        }
    }
    public function updatedIsmultiple($value): void
    {
        // dd($value);
        if ($value == 'yes' && $this->field_type == 'select') {
            $this->column_type = 'jsonb';
        } else {
            $this->updatedFieldType($this->field_type);
        }
    }

    public function updatedIsconfirm($value): void
    {
        if ($value === 'yes') {
            $this->confirmOptions = $this->fields;
            // dd($this->confirmOptions);
        }
    }
    public function updatedIsdependent($value): void
    {
        if ($value === 'yes') {
            $this->depenentOptions = $this->fields;
            $this->depvalueradio = true;
        } else {
            $this->reset(['depenent_on', 'depvalues', 'depvaluesopt']);
            $this->depvalueradio = false;
        }
    }
    public function updatedDepenentOn($value)
    {
        $this->depenent_on = $value;
        $this->reset(['isdependentvalue', 'depvalues']);
        if ($value) {
            $this->depvalueradio = true;   // radio show
        } else {
            $this->depvalueradio = false;
            $this->isdependentvalue = 'no';
            $this->depvaluesopt = [];
            $this->depvalues = [];
        }
    }
    public function updatedIsdependentvalue($value): void
    {
        if ($value === 'yes' && $this->depenent_on) {
            $field = collect($this->fields)->firstWhere('field_name', $this->depenent_on);
            $this->depvaluesopt = $field['options'] ?? [];
        } else {
            $this->depvaluesopt = [];
            $this->depvalues = [];
        }
    }
    public function updatedKeyType($value): void
    {
        if ($value !== 'foreign') {
            $this->reset(['fk_table', 'fk_column', 'fkColumns']);
        } else {
            $this->fkTables = DB::select("
        SELECT schemaname || '.' || tablename AS table_name
        FROM pg_tables
        WHERE schemaname NOT IN ('pg_catalog', 'information_schema')
        ORDER BY table_name
    ");
        }
    }
    public function updatedFkTable($value): void
    {
        if (!$value) return;

        [$schema, $table] = explode('.', $value);
        $this->fkColumns = DB::select("
                SELECT DISTINCT a.attname AS column_name
                FROM pg_index i
                JOIN pg_attribute a ON a.attrelid = i.indrelid
                AND a.attnum = ANY(i.indkey)
                JOIN pg_class c ON c.oid = i.indrelid
                JOIN pg_namespace n ON n.oid = c.relnamespace
                WHERE n.nspname = ?
                AND c.relname = ?
                AND (i.indisprimary = true OR i.indisunique = true)
                ORDER BY a.attname
            ", [$schema, $table]);
    }

    public function updatedFkColumn($value): void
    {
        // dd($value);
        if (!$this->fk_table || !$value) return;
        [$schema, $table] = explode('.', $this->fk_table);
        $column = DB::table('information_schema.columns')
            ->where('table_schema', $schema)
            ->where('table_name', $table)
            ->where('column_name', $value)
            ->first();
        // dd($column);
        if ($column) {
            $map = [
                'integer' => 'integer',
                'bigint' => 'unsignedBigInteger',
                'smallint' => 'smallInteger',
                'character varying' => 'string',
                'boolean' => 'boolean',
                'date' => 'date',
                'jsonb' => 'jsonb',
            ];
            $this->column_type = $map[$column->data_type] ?? 'string';
        }
    }
    /* ---------------- MODAL ---------------- */
    public function openFieldModal(): void
    {
        $this->resetFieldForm();
        $this->showModal = true;
    }
    public function editField(int $index): void
    {
        $this->editIndex = $index;

        $field = $this->fields[$index];
        $this->fill(collect($field)->except(['validation_rule', 'options'])->toArray());
        $this->validation_rule = !empty($field['validation_rule'])
            ? explode('|', $field['validation_rule'])
            : [];
        $this->options = $field['options'] ?? [];
        $this->showModal = true;
    }
    public function closeModal(): void
    {
        $this->resetFieldForm();
        $this->showModal = false;
        $this->editIndex = null;
    }
    public function updatedDefaultValue($value)
    {
        $this->defaultOptions = [];
        if ($this->is_choose_default === 'yes') {
            if (isset($this->default_values[$value])) {
                $this->defaultOptions = $this->default_values[$value];
                if (empty($this->defaultOptions)) {
                    $this->field_class = strtolower(
                        preg_replace('/[^a-zA-Z0-9]+/', '_', $value)
                    );
                }
            }
        } else {
            $this->defaultOptions = [];
            $this->field_class = null;
        }
    }
    /* ---------------- SAVE FIELD ---------------- */
    public function saveField(): void
    {


        if (in_array($this->field_type, ['select', 'checkbox', 'radio']) && $this->is_choose_default === 'yes') {
            $this->options = $this->defaultOptions;
        }
        // dd($this->options);
        // dd($this->all());
        $this->validate([
            'tab_name'  => 'required|min:3|max:50|unique:master_tabs,tab_name',
            'column_name' => 'required',
            'column_type' => 'required',
            'field_name'  => [
                'required',
                'min:3',
                'max:50',
                Rule::unique('scheme_tab_basefields', 'field_name')
                    ->where(fn($q) => $q->where('tab_code', $this->tab_code)),
            ],
            'field_type'  => 'required',
            'is_append_multiple' => 'required',
            'level_name' => [
                'required',
                'min:3',
                'max:50',
                Rule::unique('scheme_tab_basefields', 'level_name')
                    ->where(fn($q) => $q->where('tab_code', $this->tab_code)),
            ],
            'field_id' => [
                'required',
                'min:3',
                'max:50',
                Rule::unique('scheme_tab_basefields', 'field_id')
                    ->where(fn($q) => $q->where('tab_code', $this->tab_code)),
            ],
            'validation_rule' => 'required|array|min:1',
            'options' => in_array($this->field_type, ['select', 'checkbox', 'radio']) && $this->is_choose_default === 'no'
                ? 'required|array|min:1'
                : 'nullable',
            'key_type' => 'required',
            'is_under_section' => 'required',
            'section_id' => $this->is_under_section == 'yes'
                ? 'required:if|exists:section_level_masters,section_level_id'
                : 'nullable',
            'is_choose_default' => 'required|in:yes,no',
            'default_value' => $this->is_choose_default == 'yes'
                ? 'required' : 'nullable',
            'is_multiple' => $this->field_type == 'select'
                ? 'required|in:yes,no'
                : 'nullable',
            'isconfirm' => 'required|in:yes,no',
            'confirm_of' => $this->isconfirm == 'yes'
                ? 'required' : 'nullable',
            'isdependent' => $this->isconfirm == 'yes'
                ? 'required|in:yes,no' : 'nullable',
            'depenent_on' => $this->isdependent == 'yes'
                ? 'required' : 'nullable',
            'isdependentvalue' => $this->isdependent == 'yes'
                ? 'required|in:yes,no' : 'nullable',
            'depvalues' => $this->isdependentvalue == 'yes' && !empty($this->depvaluesopt)
                ? 'required' : 'nullable',
            'nullable' => 'required|in:yes,no',
            'default_enabled' => 'required|in:yes,no',
            'db_default_value' => $this->default_enabled == 'yes'
                ? 'required' : 'nullable',
            'mendetory' => 'required|in:yes,no',
        ]);
        if ($this->model_name) {
            $checkModelName = MasterTab::where('tab_model_name', $this->model_name)->first();
            if ($checkModelName) {
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => 'Model name already exists. Please choose a different name.',
                ]);
                return;
            }
        }

        $validationRule = !empty($this->validation_rule)
            ? collect($this->validation_rule)
            ->flatten()
            ->filter(fn($v) => is_string($v))
            ->implode('|')
            : null;
        $this->field_position = (int) (
            SchemeTabBasefield::where('tab_code', $this->tab_code)
            ->max('field_position') ?? 0
        );
        $this->options = collect($this->options ?? [])
            ->flatten()
            ->filter(fn($v) => is_string($v) && $v !== '')
            ->values()
            ->mapWithKeys(fn($value, $index) => [$index + 1 => $value])
            ->toArray();
        $data = [
            'column_name' => $this->column_name,
            'column_type' => $this->column_type,
            'length'      => $this->length,
            'nullable'    => $this->nullable,
            'default_enabled' => $this->default_enabled,
            'db_default_value' => $this->default_enabled === 'yes'
                ? $this->db_default_value
                : null,
            'key_type' => $this->key_type,
            'key_name' => $this->key_name,
            'key_ref'  => $this->key_reference,
            'level_name' => $this->level_name,
            'field_id'   => $this->field_id,
            'field_name' => $this->field_name,
            'field_type' => $this->field_type,
            'view_type'  => $this->view_type,
            'validation_rule' => $validationRule,
            'section_id' => $this->is_under_section === 'yes'
                ? $this->section_id
                : null,
            'is_multiple' => $this->is_multiple === 'yes' ? 'yes' : 'no',
            'confirm_of' => $this->isconfirm === 'yes'
                ? $this->confirm_of
                : null,
            'dependent_on' => $this->isdependent === 'yes' ? $this->depenent_on : null,
            'dep_values' => $this->isdependentvalue === 'yes'
                ? $this->depvalues
                : null,
            'fk_table' => $this->key_type === 'foreign'
                ? $this->fk_table
                : null,
            'fk_column' => $this->key_type === 'foreign'
                ? $this->fk_column
                : null,
            'options' => in_array($this->field_type, ['select', 'checkbox', 'radio'])
                ? $this->options
                : null,
            'is_choose_default' => $this->is_choose_default,
            'default_value' => $this->is_choose_default === 'yes'
                ? $this->default_value
                : null,
            'mendetory' => $this->mendetory,
            'field_class' => $this->is_choose_default === 'yes' ? $this->field_class : null,
            'field_position' => $this->field_position,
            'isconfirm' => $this->isconfirm,
            'isdependent' => $this->isdependent,
            'isdependentvalue' => $this->isdependentvalue,
            'is_under_section' => $this->is_under_section,
            'section_type' => $this->section_type,
            'regex' => $this->regex,
        ];

        if ($this->editIndex !== null) {
            $this->fields[$this->editIndex] = $data;
        } else {
            $this->fields[] = $data;
        }
        $this->closeModal();
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }
    /* ---------------- FINAL SUBMIT ---------------- */
    public function finalSubmit(DynamicModelMigrationService $service): void
    {
        // dd($this->fields);
        // $this->reset('field_class');
        // $validationRules = collect($field['validation_rule'] ?? [])
        //     ->flatten()
        //     ->filter(fn($v) => is_string($v) && $v !== '')
        //     ->values()
        //     ->toArray();

        // /* ---------- OPTIONS ---------- */
        // $options = collect($this->options ?? [])
        //     ->flatten()
        //     ->filter(fn($v) => is_string($v) && $v !== '')
        //     ->values()
        //     ->mapWithKeys(fn($value, $index) => [$index + 1 => $value])
        //     ->toArray();


        // foreach ($this->fields as $field) {
        //     dump($field);
        //     $validationRule = $field['validation_rule'] ?? '';
        //     if (($field['isconfirm'] ?? 'no') === 'yes') {
        //         $validationRule .= ($validationRule ? '|' : '') . 'same:' . $field['confirm_of'];
        //     }

        //     dump([
        //         'fields' => $this->fields,
        //         'scheme_id' => $this->scheme_id ?? 0,
        //         'level_name' => $field['level_name'],
        //         'field_name' => $field['field_name'],
        //         'field_id'   => $field['field_id'],
        //         'field_type' => $field['field_type'],
        //         'options' => $field['options'] ?? null,
        //         'is_common' => true,
        //         'db_colunm' => $field['column_name'],
        //         'is_mendetory' => $field['mendetory'] === 'yes' ? 1 : 0,
        //         'tab_code' => $this->tab_code,
        //         'validation_rule' => $validationRule ?: null,
        //         'regex' => $field['regex'] ?? null,
        //         'is_multiple' => $field['field_type'] === 'select'
        //             ? ($this->is_multiple === 'yes')
        //             : false,
        //         'field_position' => $this->field_position + 1,
        //         'is_active' => true,
        //         'confirm_of' => $field['isconfirm'] === 'yes'
        //             ? $field['confirm_of']
        //             : null,
        //         'dependent_on' => $field['isdependent'] === 'yes'
        //             ? $field['dependent_on']
        //             : null,
        //         'dependent_on_values' => $field['isdependentvalue'] === 'yes'
        //             ? json_encode($field['dependent_on_values'])
        //             : null,
        //         'field_class' => $field['field_class'] ?? null,
        //         'section_level_id' => $field['is_under_section'] === 'yes'
        //             ? $field['section_id']
        //             : null,
        //         'section_level_type' => $field['is_under_section'] === 'yes'
        //             ? 0 : null,
        //     ]);
        // }
        // dd('stop');

        // $validationRules = collect($this->validation_rule ?? [])
        //     ->flatten()
        //     ->filter(fn($v) => is_string($v) && $v !== '')
        //     ->values()
        //     ->toArray();

        /* ---------- OPTIONS ---------- */
        // $options = collect($this->options ?? [])
        //     ->flatten()
        //     ->filter(fn($v) => is_string($v) && $v !== '')
        //     ->values()
        //     ->mapWithKeys(fn($value, $index) => [$index + 1 => $value])
        //     ->toArray();
        $this->is_append_multiple = $this->is_append_multiple === 'yes' ? 'yes' : 'no';

        DB::transaction(function () use ($service) {
            $tabDetails = MasterTab::create([
                'tab_name'       => $this->tab_name,
                'tab_short_name' => $this->tab_short_name,
                'tab_code'       => $this->tab_code,
                'tab_model_name'     => $this->model_name,
                // 'table_name'     => $this->table_name,
            ]);
            $service->generate($this->tab_name, $this->fields, $this->is_append_multiple);
            /* ---------- BASE FIELD ---------- */
            foreach ($this->fields as $field) {
                $validationRule = $field['validation_rule'] ?? '';
                if (($field['isconfirm'] ?? 'no') === 'yes') {
                    $validationRule .= ($validationRule ? '|' : '') . 'same:' . $field['confirm_of'];
                }
                if (($field['isdependent'] ?? 'no') === 'yes') {
                    if (str_contains($validationRule, 'required')) {
                        if (($field['isdependentvalue'] ?? 'no') === 'yes' && !empty($field['dep_values'])) {
                            $vals = is_array($field['dep_values']) ? $field['dep_values'] : [];
                            $values = ',' . implode(',', $vals);
                            $validationRule = str_replace('required', 'required_if:' . 'formData.' . $field['dependent_on'] . $values, $validationRule);
                        }
                    }
                }
                $tabFieldDetails = SchemeTabBasefield::create([
                    'scheme_id' => $this->scheme_id ?? 0,
                    'level_name' => $field['level_name'],
                    'field_name' => $field['field_name'],
                    'field_id'   => $field['field_id'],
                    'field_type' => $field['field_type'],
                    'options' => $field['options'] ?? null,
                    // 'options' => in_array($field['field_type'], ['select', 'checkbox', 'radio'])
                    //     ? (
                    //         $field['is_choose_default'] === 'yes'
                    //         ? $this->defaultOptions
                    //         : $options
                    //     )
                    //     : null,
                    'is_common' => true,
                    'db_colunm' => $field['column_name'],
                    'is_mendetory' => $field['mendetory'] === 'yes' ? 1 : 0,
                    'tab_code' => $this->tab_code,
                    'validation_rule' => $validationRule ?: null,
                    'regex' => $field['regex'] ?? null,
                    'is_multiple' => $field['field_type'] === 'select'
                        ? ($this->is_multiple === 'yes')
                        : false,
                    'field_position' => $this->field_position + 1,
                    'is_active' => true,
                    // 'confirm_of' => $field['isconfirm'] === 'yes'
                    //     ? $field['confirm_of']
                    //     : null,
                    'dependent_on' => $field['isdependent'] === 'yes'
                        ? $field['dependent_on']
                        : null,
                    'dependent_on_values' => $field['isdependentvalue'] === 'yes'
                        ? json_encode($field['dep_values'], JSON_FORCE_OBJECT)
                        : null,
                    'field_class' => $field['field_class'] ?? null,
                    'section_level_id' => $field['is_under_section'] === 'yes'
                        ? $field['section_id']
                        : null,
                    'section_level_type' => $field['is_under_section'] === 'yes'
                        ? 0 : null,
                ]);
            }
        });

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Tab, fields, and migration created successfully.',
        ]);
        // }
    }

    private function resetFieldForm(): void
    {
        $this->reset([
            'column_name',
            'column_type',
            'length',
            'options',
            'nullable',
            'default_enabled',
            'default_value',
            'db_default_value',
            'constant_value',
            'key_type',
            'key_name',
            'key_reference',
            'level_name',
            'field_id',
            'field_name',
            'field_type',
            'view_type',
            'field_class',
            'validation_rule',
            'is_under_section',
            'section_id',
            'is_multiple',
            'is_choose_default',
            'isconfirm',
            'confirm_of',
            'isdependent',
            'depenent_on',
            'isdependentvalue',
            'depvalues',
            'option_input',
            'field_position',
            'regex',
        ]);
    }

    public function render()
    {
        return view('livewire.master-tab-create');
    }
}
