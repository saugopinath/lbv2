<?php

namespace App\Livewire;

use App\Models\MasterTab;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Services\DynamicModelMigrationService;

class MasterTabCreate extends Component
{
    /* ---------------- TAB INFO ---------------- */
    public string $tab_name = '';
    public string $tab_short_name = '';
    public string $tab_code = '';

    public string $model_name;
    public string $table_name;

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

    /* ---------------- FORM FIELD INFO ---------------- */
    public ?string $field_id = null;
    public ?string $field_name = null;
    public ?string $field_label = null;
    public string $field_type = 'text';
    public ?string $validation_rule = null;

    /* ---------------- INIT ---------------- */
    public function mount(): void
    {
        // $this->model_name = Str::studly(Str::singular($this->tab_name));
        // $this->table_name = Str::snake($this->tab_name);
    }

    /* ---------------- AUTO DB COLUMN ---------------- */
    public function updatedColumnName($value): void
    {
        $this->db_column = Str::snake($value);
    }

    public function updatedTabName($value)
    {
        $this->model_name = Str::studly(Str::singular($value));
        $this->table_name = Str::snake(Str::pluralStudly($value));
        $this->tab_short_name = Str::snake($value);
        $this->tab_code = (MasterTab::max('tab_code') ?? 100) + 001;
    }
    public function updatedFieldName($value)
    {
        $this->field_id = Str::snake($value);
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
            'column_name' => 'required|string',
            'column_type' => 'required|string',
            'field_name'  => 'required|string',
            'field_label' => 'required|string',
        ]);

        $data = [
            'column_name'     => $this->column_name,
            'db_column'       => $this->db_column,
            'column_type'     => $this->column_type,
            'length'          => $this->length,
            'nullable'        => $this->nullable,
            'default_value'   => $this->default_enabled ? $this->default_value : null,
            'constant_value'  => $this->constant_value,
            'key_type'        => $this->key_type,
            'field_id'        => $this->field_id,
            'field_name'      => $this->field_name,
            'field_label'     => $this->field_label,
            'field_type'      => $this->field_type,
            'validation_rule' => $this->validation_rule,
            'description'     => $this->nullable ? 'Nullable' : 'Not Nullable',
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
        $this->validate([
            'tab_name' => 'required|string',
            'fields'   => 'required|array|min:1',
        ]);

        $newTab = MasterTab::create([
            'tab_name'       => $this->tab_name,
            'tab_short_name' => $this->tab_short_name,
            'tab_code'       => $this->tab_code,
            'model_name'     => $this->model_name,
            'table_name'     => $this->table_name,
        ]);
        $service->generate($this->tab_name, $this->fields);

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Final submission completed. Migration executed successfully.',
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
            'field_id',
            'field_name',
            'field_label',
            'field_type',
            'validation_rule',
        ]);
    }

    public function render()
    {
        return view('livewire.master-tab-create');
    }
}
