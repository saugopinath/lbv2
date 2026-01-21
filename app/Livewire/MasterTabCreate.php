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
    public string $column_type = 'string';
    public ?int $length = null;
    public bool $nullable = false;
    public bool $default_enabled = false;
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

    /* ---------------- VIEW HELPERS ---------------- */
    public bool $isdepenentsec = false;
    public bool $depvalueradio = false;

    /* ---------------- DROPDOWN DATA ---------------- */
    public $fieldTypes = [];
    public array $validationRuleOptions = [];

    /* ========================================================= */

    public function mount(): void
    {
        $this->fieldTypes = FromFieldType::all();
        // dd($this->fieldTypes);
        $this->validationRuleOptions = ValidationRule::all()
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

    /* ---------------- AUTO GENERATED ---------------- */
    public function updatedTabName($value): void
    {
        $this->model_name = Str::studly(Str::singular($value));
        $this->table_name = Str::snake(Str::pluralStudly($value));
        $this->tab_short_name = Str::snake($value);

        $this->tab_code = DB::transaction(function () {
            return (MasterTab::lockForUpdate()->max('tab_code') ?? 100) + 1;
        });
    }

    public function updatedColumnName($value): void
    {
        $this->db_column = Str::snake($value);
    }

    public function updatedFieldName($value): void
    {
        $this->field_id = Str::snake($value);
    }

    public function updatedIsUnderSection($value): void
    {
        if ($value === 'yes') {
            $this->sections = MasterSection::orderBy('section_name')->get();
        } else {
            $this->sections = [];
            $this->section_id = null;
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

    /* ---------------- MODAL ---------------- */
    public function openFieldModal(): void
    {
        $this->resetFieldForm();
        $this->showModal = true;
    }

    public function editField(int $index): void
    {
        $this->editIndex = $index;
        $this->fill($this->fields[$index]);
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
            'view_type'   => 'required',
        ]);

        $this->fields[] = [
            'column_name' => $this->column_name,
            'db_column'   => $this->db_column,
            'column_type' => $this->column_type,
            'length'      => $this->length,
            'nullable'    => $this->nullable,
            'default'     => $this->default_enabled ? $this->default_value : null,
            'key_type'    => $this->key_type,
            'key_name'    => $this->key_name,
            'key_ref'     => $this->key_reference,

            'level_name'  => $this->level_name,
            'field_id'    => $this->field_id,
            'field_name'  => $this->field_name,
            'field_label' => $this->field_label,
            'field_type'  => $this->field_type,
            'view_type'   => $this->view_type,
            'rules'       => $this->validation_rule,

            'section_id'  => $this->is_under_section === 'yes' ? $this->section_id : null,
            'is_multiple' => $this->is_multiple === 'yes',
            'confirm_of'  => $this->isconfirm === 'yes' ? $this->confirm_of : null,
            'dependent_on' => $this->isdependent === 'yes' ? $this->depenent_on : null,
            'dep_values'  => $this->isdependentvalue === 'yes' ? $this->depvalues : null,
        ];

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
        DB::transaction(function () use ($service) {
            MasterTab::create([
                'tab_name'       => $this->tab_name,
                'tab_short_name' => $this->tab_short_name,
                'tab_code'       => $this->tab_code,
                'model_name'     => $this->model_name,
                'table_name'     => $this->table_name,
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
            'db_column',
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
