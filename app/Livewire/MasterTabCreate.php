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

class MasterTabCreate extends Component
{
    /* ---------------- TAB INFO ---------------- */
    public string $tab_name = '';
    public string $tab_short_name = '';
    public string $tab_code = '';

    public string $model_name = '';
    public string $table_name = '';

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
    public ?string $default_value = null;
    public ?string $constant_value = null;

    public string $key_type = 'none';
    public ?string $key_name = null;
    public ?string $key_reference = null;

    /* ---------------- FORM FIELD INFO ---------------- */
    public ?string $level_name = null;
    public ?string $field_id = null;
    public ?string $field_name = null;
    public ?string $field_label = null;
    public string $field_type = '';
    public ?string $view_type = null;

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
    public ?int $confirm_of = null;
    public  $confirmOptions = [];

    public string $isdependent = 'no';
    public ?int $depenent_on = null;
    public  $depenentOptions = [];

    public string $isdependentvalue = 'no';
    public  $depvalues = [];
    public array $depvaluesopt = [];

    public bool $isdepenentsec = false;
    public bool $depvalueradio = false;
    public $fieldTypes = [];
    public array $validationRuleOptions = [];

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
    public function updatedFieldType($value): void
    {
        $map = [
            'text'      => 'string',
            'password'  => 'string',
            'number'    => 'integer',
            'date'      => 'date',
            'textarea'  => 'text',
            'select'    => 'string',
            'radio'     => 'smallInteger',
            'checkbox'  => 'smallInteger',
            'file'      => 'text',
        ];
        $this->column_type = $map[$value] ?? 'string';
        if (!in_array($value, ['select', 'radio', 'checkbox'])) {
            $this->is_multiple = 'no';
            $this->is_choose_default = 'no';
        }
    }
    public function updatedIsconfirm($value): void
    {
        if ($value === 'yes') {
            $this->confirmOptions = SchemeTabBasefield::all();
        }
    }
    public function updatedIsdependent($value): void
    {
        if ($value === 'yes') {
            $this->depenentOptions = SchemeTabBasefield::all();
            $this->depvalueradio = true;
        } else {
            $this->reset(['depenent_on', 'depvalues', 'depvaluesopt']);
            $this->depvalueradio = false;
        }
    }
    public function updatedDepenentOn($value): void
    {
        if ($value) {
            $this->depvalueradio = true;
        }
    }
    public function updatedIsdependentvalue($value): void
    {
        if ($value === 'yes' && $this->depenent_on) {
            $ram = SchemeTabBasefield::find($this->depenent_on);
            $this->depvaluesopt = is_array($ram?->options)
                ? $ram->options
                : [];
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

        // Fill everything EXCEPT validation_rule
        $this->fill(collect($field)->except('validation_rule')->toArray());

        // Convert string → array for multiselect
        $this->validation_rule = !empty($field['validation_rule'])
            ? explode('|', $field['validation_rule'])
            : [];

        $this->showModal = true;
    }
    public function closeModal(): void
    {
        $this->resetFieldForm();
        $this->showModal = false;
        $this->editIndex = null;
    }

    /* ---------------- SAVE FIELD ---------------- */
    public function saveField(): void
    {
        $this->validate([
            'column_name' => 'required',
            'column_type' => 'required',
            'field_name'  => 'required',
            'field_type'  => 'required',
        ]);

        $validationRule = !empty($this->validation_rule)
            ? collect($this->validation_rule)
            ->flatten()
            ->filter(fn($v) => is_string($v))
            ->implode('|')
            : null;
        $data = [
            'column_name' => $this->column_name,
            'column_type' => $this->column_type,
            'length'      => $this->length,
            'nullable'    => $this->nullable === 'yes',
            'default_value' => $this->default_enabled === 'yes'
                ? $this->default_value
                : null,

            'key_type' => $this->key_type,
            'key_name' => $this->key_name,
            'key_ref'  => $this->key_reference,

            'level_name' => $this->level_name,
            'field_id'   => $this->field_id,
            'field_name' => $this->field_name,
            'field_label' => $this->field_label,
            'field_type' => $this->field_type,
            'view_type'  => $this->view_type,

            'validation_rule' => $validationRule,

            'section_id' => $this->is_under_section === 'yes'
                ? $this->section_id
                : null,

            'is_multiple' => $this->is_multiple === 'yes',

            'confirm_of' => $this->isconfirm === 'yes'
                ? $this->confirm_of
                : null,

            'dependent_on' => $this->isdependent === 'yes'
                ? $this->depenent_on
                : null,

            'dep_values' => $this->isdependentvalue === 'yes'
                ? $this->depvalues
                : null,
            'fk_table' => $this->key_type === 'foreign'
                ? $this->fk_table
                : null,

            'fk_column' => $this->key_type === 'foreign'
                ? $this->fk_column
                : null,
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
        // dd(
        //     $this->tab_name,
        //     $this->tab_short_name,
        //     $this->tab_code,
        //     $this->model_name,
        //     $this->table_name,
        //     $this->fields
        // );
        DB::transaction(function () use ($service) {
            MasterTab::create([
                'tab_name'       => $this->tab_name,
                'tab_short_name' => $this->tab_short_name,
                'tab_code'       => $this->tab_code,
                'tab_model_name'     => $this->model_name,
                // 'table_name'     => $this->table_name,
            ]);

            $service->generate($this->tab_name, $this->fields);
        });

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Tab, fields, and migration created successfully.',
        ]);
    }

    /* ---------------- RESET ---------------- */
    private function resetFieldForm(): void
    {
        $this->reset([
            'column_name',
            'column_type',
            'length',
            'nullable',
            'default_enabled',
            'default_value',
            'constant_value',
            'key_type',
            'key_name',
            'key_reference',
            'level_name',
            'field_id',
            'field_name',
            'field_label',
            'field_type',
            'view_type',
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
            'depvalues'
        ]);
    }

    public function render()
    {
        return view('livewire.master-tab-create');
    }
}
