<?php

namespace App\Livewire\DynamicForm;

use App\Models\FromFieldAttribute;
use App\Models\MasterSection;
use App\Models\OtherDetails;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class RenderDynamicForm extends Component
{
    public int $schemeId;
    public int $applicationId;
    public array $fields = [];
    public array $sections = [];
    public array $formData = [];
    use WithFileUploads;
    public function mount(int $schemeId, int $applicationId)
    {
        $this->schemeId = $schemeId;
        $this->applicationId = $applicationId;
        $this->fields = FromFieldAttribute::where('scheme_id', $this->schemeId)
            ->where('is_active', true)
            ->orderBy('created_at')
            ->get()
            ->groupBy('section_id')
            ->toArray();
        $this->sections = MasterSection::where('scheme_id', $this->schemeId)
            ->get()
            ->keyBy('id')
            ->toArray();

        foreach ($this->fields as $group) {
            foreach ($group as $field) {
                if (in_array($field['field_type'], ['checkbox', 'select', 'radio'])) {
                    $this->formData[$field['field_label']] = [];
                } else {
                    $this->formData[$field['field_label']] = null;
                }
            }
        }
        // dd($field);
        // dd($field['level_name']);
        $existing = OtherDetails::where('application_id', $applicationId)->first();
        if ($existing && is_array($existing->details)) {
            foreach ($existing->details as $key => $value) {
                $this->formData[$key] = $value;
            }
        }
    }
    private function fileToBase64(TemporaryUploadedFile $file): array
    {
        return [
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'base64' => base64_encode(
                file_get_contents($file->getRealPath())
            ),
        ];
    }
    public function save()
    {
        // dd($this->formData);
        $this->validate(
            $this->buildValidationRules()
        );
        $payload = [];
        foreach ($this->formData as $key => $value) {
            if ($value instanceof TemporaryUploadedFile) {
                $payload[$key] = $this->fileToBase64($value);
            } else {
                $payload[$key] = $value;
            }
        }
        // dd($payload);
        OtherDetails::updateOrCreate(
            [
                'application_id' => $this->applicationId,
                'scheme_id'      => $this->schemeId,
            ],
            [
                'details' => $payload,
            ]
        );
       $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Application submitted successfully!'
        ]);
    }
    private function buildValidationRules(): array
    {
        $rules = [];
        foreach ($this->fields as $group) {
            foreach ($group as $field) {

                $rule = $field['validation_rule'];
                // Skip empty rules
                if (!$rule || $rule === 'nullable') {
                    continue;
                } else {
                    $rules["formData.{$field['field_label']}"] = $rule;
                }
            }
        }
        // dd($rules);
        return $rules;
    }
    protected function validationAttributes(): array
    {
        $attributes = [];
        foreach ($this->fields as $group) {
            foreach ($group as $field) {
                $attributes["formData.{$field['field_label']}"]
                    = $field['level_name'];
            }
        }
        return $attributes;
    }
    public function render()
    {
        return view('livewire.dynamic-form.render-dynamic-form');
    }
}
