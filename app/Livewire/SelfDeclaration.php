<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AcceptRejectInfo;
use App\Models\Codemaster;

class SelfDeclaration extends Component
{
    public $mode, $application_id;
    public $resident = false;
    public $no_govt_salary = false;
    public $info_true = false;
    public $aadhaar_consent = false;

    public function mount($mode = null, $application_id = null)
    {
        $this->mode = $mode;
        if ($application_id != null) {
            $this->application_id = $application_id;
            $app_det = DraftBeneficiaryPersonal::with('declaration')->where('application_id', $application_id)->first();
            if ($app_det->declaration) {
                $this->resident = $app_det->declaration->is_resident;
                $this->no_govt_salary = $app_det->declaration->earn_monthly_remuneration;
                $this->info_true = $app_det->declaration->info_genuine_decl;
                $this->aadhaar_consent = $app_det->declaration->av_status;
            }
        }
    }

    public function rules()
    {
        return [
            'resident' => 'nullable|boolean',
            'no_govt_salary' => 'nullable|boolean',
            'info_true' => 'nullable|boolean',
            'aadhaar_consent' => 'nullable|boolean',
        ];
    }

    public function save()
    {
        try {
            $validated = $this->validate($this->rules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('hideLoader');
            throw $e;
        }
        $app_det = DraftBeneficiaryDeclaration::where('application_id', $this->application_id)->first();
        DB::beginTransaction();
        try {
            if ($this->mode === null && empty($app_det)) {
                $application_id = $this->application_id;
                $DraftBeneficiaryDeclaration = new DraftBeneficiaryDeclaration;
                $DraftBeneficiaryDeclaration->application_id = $application_id;
                $DraftBeneficiaryDeclaration->is_resident = $validated['resident'];
                $DraftBeneficiaryDeclaration->earn_monthly_remuneration = $validated['no_govt_salary'];
                $DraftBeneficiaryDeclaration->info_genuine_decl = $validated['info_true'];
                $DraftBeneficiaryDeclaration->av_status = $validated['aadhaar_consent'];
                $DraftBeneficiaryDeclaration->created_by = Auth::id();
                $DraftBeneficiaryDeclaration->save();

                $draftbenPar = DraftBeneficiaryPersonal::find($application_id);
                $draftbenPar->next_level_role_id = Codemaster::getIdByCode(22);
                $draftbenPar->is_final_submit = 1;
                $draftbenPar->save();

                $AcceptRejectInfo = new AcceptRejectInfo;
                $AcceptRejectInfo->application_id = $application_id;
                $AcceptRejectInfo->beneficiary_id = $draftbenPar->beneficiary_id;
                $AcceptRejectInfo->ip_address = request()->ip();
                $AcceptRejectInfo->user_id = Auth::id();
                $AcceptRejectInfo->browser = request()->header('User-Agent');
                $AcceptRejectInfo->model_name = null;
                $AcceptRejectInfo->op_type = Codemaster::getIdByCode(22);
                $AcceptRejectInfo->revert_reason_cause_id = null;
                $AcceptRejectInfo->revert_reason_remarks = null;
                $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $application_id)
                    ->latest('id')
                    ->value('id') ?? null;
                $AcceptRejectInfo->save();
            } else {
                $DraftBeneficiaryDeclaration = DraftBeneficiaryDeclaration::find($this->application_id);
                $DraftBeneficiaryDeclaration->is_resident = $validated['resident'];
                $DraftBeneficiaryDeclaration->earn_monthly_remuneration = $validated['no_govt_salary'];
                $DraftBeneficiaryDeclaration->info_genuine_decl = $validated['info_true'];
                $DraftBeneficiaryDeclaration->av_status = $validated['aadhaar_consent'];
                $DraftBeneficiaryDeclaration->created_by = Auth::id();
                $DraftBeneficiaryDeclaration->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('hideLoader');
            throw $e;
        }
        // $this->dispatch('selfDec');
         $this->dispatch('selfDec', [
            'message' => "Self Decleration uploaded successfully for the application id: {$this->application_id}"
        ]);
        $this->dispatch('hideLoader');
        // $this->dispatch('toastr', [
        //     'type' => 'success',
        //     'message' => 'Application submitted successfully!'
        // ]);
    }

    public function render()
    {
        return view('livewire.self-declaration');
    }
}
