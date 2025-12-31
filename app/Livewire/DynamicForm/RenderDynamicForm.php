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
    public function mount( $schemeId = null,  $application_id = null, $mode = null)
    {
        // dd('')
        // dump($schemeId);
        // dd($application_id);
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
        // dd($field);
        // dd($field['level_name']);
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
    public function shouldShowField($field)
{
    // if not dependent → always show
    if (empty($field['dependent_on'])) {
        return true;
    }

    // parent field label
    $parentLabel = $field['dependent_on_label']; 
    // e.g. "caste"

    $parentValue = $this->formData[$parentLabel] ?? null;

    // SC / ST values (based on your JSON keys)
    return in_array($parentValue, ['1', '2']);
}

    public function render()
    {
        return view('livewire.dynamic-form.render-dynamic-form');
    }
}
