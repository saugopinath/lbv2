<?php

namespace App\Livewire\DynamicForm;

use App\Models\AcceptRejectInfo;
use App\Models\Codemaster;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\FromFieldAttribute;
use App\Models\MasterSection;
use App\Models\OtherDetails;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class RenderDynamicForm extends Component
{
    public  $schemeId;
    public $mode;
    public  $application_id;
    public array $fields = [];
    public array $sections = [];
    public array $formData = [];

    use WithFileUploads;
    public function mount($schemeId = null,  $application_id = null, $mode = null)
    {
        $this->mode = $mode;
        $this->schemeId = $schemeId;
        $this->application_id = $application_id;
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
        $existing = OtherDetails::where('application_id', $application_id)->first();
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
        OtherDetails::updateOrCreate(
            [
                'application_id' => $this->application_id,
                'scheme_id'      => $this->schemeId,
            ],
            [
                'details' => $payload,
            ]
        );
        $draftbenPar = DraftBeneficiaryPersonal::find($this->application_id);
        $draftbenPar->next_level_role_id = Codemaster::getIdByCode(22);
        $draftbenPar->is_final_submit = 1;
        $draftbenPar->save();
        $AcceptRejectInfo = new AcceptRejectInfo;
        $AcceptRejectInfo->application_id = $this->application_id;
        $AcceptRejectInfo->beneficiary_id = $draftbenPar->beneficiary_id;
        $AcceptRejectInfo->ip_address = request()->ip();
        $AcceptRejectInfo->user_id = Auth::id();
        $AcceptRejectInfo->browser = request()->header('User-Agent');
        $AcceptRejectInfo->model_name = null;
        $AcceptRejectInfo->op_type = Codemaster::getIdByCode(22);
        $AcceptRejectInfo->revert_reason_cause_id = null;
        $AcceptRejectInfo->revert_reason_remarks = null;
        $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $this->application_id)
            ->latest('id')
            ->value('id') ?? null;
        $AcceptRejectInfo->save();

        $this->dispatch('selfDec1');
        $this->dispatch('hideLoader');
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
                if (!$rule || $rule === 'nullable') {
                    continue;
                } else {
                    $rules["formData.{$field['field_label']}"] = $rule;
                }
            }
        }
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
    public function shouldShowField($field)
    {
        if (empty($field['dependent_on'])) {
            return true;
        }

        $allFields = collect($this->fields)->flatten(1);
        $parentField = $allFields->firstWhere('id', $field['dependent_on']);

        if (!$parentField) {
            return true;
        }

        $parentLabel = $parentField['field_label'];
        $parentValue = $this->formData[$parentLabel] ?? null;

        $allowed = [];
        if (!empty($field['dependent_on_values'])) {
            $raw = is_array($field['dependent_on_values'])
                ? $field['dependent_on_values']
                : json_decode($field['dependent_on_values'], true);

            $allowed = array_values($raw);
        }

        if (is_array($parentValue)) {
            if (empty($allowed)) {
                return !empty($parentValue);
            }

            return count(array_intersect(
                array_map('strval', $parentValue),
                array_map('strval', $allowed)
            )) > 0;
        }

        if (!empty($allowed)) {
            return in_array(
                (string)$parentValue,
                array_map('strval', $allowed),
                true
            );
        }

        return $parentValue !== '' && $parentValue !== null;
    }
    public function updatedFormData() {}

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
    public function render()
    {
        return view('livewire.dynamic-form.render-dynamic-form');
    }
}
