<?php

namespace App\Livewire;

use App\Models\SchemeTabFormField;
use App\Models\SelfDeclerationBasefield;
use App\Models\ValidationRule;
use Database\Seeders\SelfDeclarationBasefieldSeeder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SchemeFieldValidationTable extends DataTableComponent
{
    public int $schemeId;
    public int $tabCode;
    public array $selectedValidations = [];
    public int $rowNumberOffset = 0;
    public ?int $perPage = 20;
    public array $validationOptions = [];
    // public array $validationOptions = [

    //     ['rule' => 'required', 'description' => 'Field is mandatory'],
    //     ['rule' => 'nullable', 'description' => 'Field is optional'],
    //     ['rule' => 'email',    'description' => 'Must be valid email'],
    //     ['rule' => 'numeric',  'description' => 'Must be numeric'],
    //     ['rule' => 'max:255',  'description' => 'Maximum 255 characters'],
    //     ['rule' => 'min:3',    'description' => 'Minimum 3 characters'],
    //     ['rule' => 'digits:10',    'description' => 'Must be 10 digits'],
    //     // ['label' => 'Required', 'value' => 'required'],
    //     // ['label' => 'Numeric',  'value' => 'numeric'],
    //     // ['label' => 'Email',    'value' => 'email'],
    //     // ['label' => 'Min 1',    'value' => 'min:1'],
    //     // ['label' => 'Max 10',   'value' => 'max:10'],
    // ];
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setPerPageAccepted([20, 25, 50, 100]);
        $this->rowNumberOffset = ($this->getPage() - 1) * $this->getPerPage();
        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm',
        ]);
        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);
        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white',
        ]);
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-4 py-3 text-white bg-violet-800 text-xs',
            ];
        });
        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-4 py-3 text-gray-700 text-center',
            ];
        });
        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
        $this->setLoadingPlaceholderEnabled();
        // $this->setConfigurableAreas([
        //     'toolbar-left-start' => 'livewire.export_excel_buttons',
        // ]);
    }
    public function mount(int $schemeId, int $tabCode)
    {
        $this->schemeId = $schemeId;
        $this->tabCode  = $tabCode;
        $this->loadExisting();
        $this->loadValidationOptions();
    }
    protected function loadExisting()
    {
        $model = $this->getFieldModel();

        $model::where('scheme_id', $this->schemeId)
            ->where('tab_code', $this->tabCode)
            ->where('is_active', true)
            ->get()
            ->each(function ($field) {
                $this->selectedValidations[$field->id] =
                    $field->validation_rule
                    ? explode('|', $field->validation_rule)
                    : [];
            });
    }
    protected function loadValidationOptions(): void
    {
        $this->validationOptions = ValidationRule::all()
            ->map(fn($rule) => [
                // Alpine code expects THIS structure
                'rule'        => $rule->rule,
                'description' => $rule->description,
            ])
            ->toArray();
    }
    protected function getFieldModel(): string
    {
        return match ($this->tabCode) {
            105     => SelfDeclerationBasefield::class,
            default => SchemeTabFormField::class,
        };
    }

    public function builder(): Builder
    {
        $model = $this->getFieldModel();

        $query = $model::query()
            ->where('scheme_id', $this->schemeId)
            ->where('tab_code', $this->tabCode)
            ->where('is_active', true)
            ->orderBy('field_position');
        return $query;
        // dd($query->get());
    }

    public function columns(): array
    {
        return [
            Column::make("No.")
                ->label(function ($value, $row) {
                    static $i = 0;
                    $i++;
                    return ($this->getPage() - 1) * $this->getPerPage() + $i;
                }),
            Column::make("ID", "id")->hideIf(true),
            Column::make("IsMandatory", "is_mandatory")->hideIf(true),
            Column::make("regex", "regex")->hideIf(true),
            Column::make('Field Name', 'field_name'),
            Column::make('Level Name', 'level_name'),
            Column::make('Type', 'field_type'),
            Column::make('Validation Rules')
                ->label(
                    fn($row) =>
                    view('coulmn_button.validation-editor', [
                        'fieldId'     => $row->id,
                        'isMandatory' => (int) $row->is_mandatory,
                    ])
                ),
            Column::make('Regex')
                ->format(function ($value, $row, Column $column) {
                    return view('coulmn_button.regex-editor', [
                        'fieldId' => $row->id,
                        'regex'   => $row->regex ?? '',
                    ])->render();
                })
                ->html(),
        ];
    }
    // public function saveValidation($fieldId): void
    // {
    //     $rules = $this->selectedValidations[$fieldId] ?? [];
    //     $field = SchemeTabFormField::findOrFail($fieldId);
    //     if ((int) $field->is_mandatory === 1 && !in_array('required', $rules)) {
    //         array_unshift($rules, 'required');
    //     }
    //     $field->update([
    //         'validation_rule' => empty($rules)
    //             ? null
    //             : implode('|', array_unique($rules)),
    //     ]);
    //     $this->dispatch('toastr', [
    //         'type'    => 'success',
    //         'message' => 'Validation Rule updated successfully',
    //     ]);
    // }
    public function saveValidation(int $fieldId): void
    {
        $rules = $this->selectedValidations[$fieldId] ?? [];
        $model = $this->getFieldModel();
        $field = $model::findOrFail($fieldId);
        if (in_array('required', $rules)) {
            $rules = array_diff($rules, ['nullable']);
        } else {
            if (!in_array('nullable', $rules)) {
                array_unshift($rules, 'nullable');
            }
        }
        if (isset($field->is_mandatory) && (int) $field->is_mandatory === 1) {
            $rules = array_diff($rules, ['nullable']);
            if (!in_array('required', $rules)) {
                array_unshift($rules, 'required');
            }
        }
        $rules = array_values(array_unique($rules));
        $field->update([
            'validation_rule' => empty($rules)
                ? null
                : implode('|', $rules),
        ]);
        $this->dispatch('toastr', [
            'type'    => 'success',
            'message' => 'Validation Rule updated successfully',
        ]);
    }
    public function saveRegex(int $id, string $regex): void
    {
        $model = $this->getFieldModel();
        $model::where('id', $id)->update([
            'regex' => $regex,
        ]);
        $this->dispatch('toastr', [
            'type'    => 'success',
            'message' => 'Regex updated successfully',
        ]);
    }
}
