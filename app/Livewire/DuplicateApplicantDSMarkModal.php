<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\DsPhase;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\DsMapRecord;
use App\Models\Codemaster;
use Illuminate\Support\Facades\DB;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\Auth;

class DuplicateApplicantDSMarkModal extends Component
{
    public $applicantId, $open;
    public $currentDate, $previouesDate, $ds_registration_no, $duaresarkarDate, $currentdsPhase;
    #[On('opendsMarkModal')]
    public function openModal($id = null)
    {
        $this->applicantId = $id;
        $this->dispatch('show-modal');
        $this->currentDate = Carbon::now()->format('Y-m-d');
        $this->previouesDate = DsPhase::where('is_current', true)->value('base_dob');
        $this->currentdsPhase = DsPhase::where('is_current', true)->value('phase_code');
    }
    public function mount() {}
    public function rules()
    {
        $rules = [
            'ds_registration_no'   => 'required',
            'duaresarkarDate'   => "required|date",
        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'ds_registration_no.*'     => 'Registration number is required.',
            'duaresarkarDate.*'    => 'DS date is required.',
        ];
    }
    public function saveDsMark()
    {
        DB::beginTransaction();
        try {
            $validated = $this->validate($this->rules());
            $targatedModel = BeneficiaryPersonalDetail::find($this->applicantId);
            $olddsres = $targatedModel->ds_registration_no;
            $olddsdate = $targatedModel->ds_date;
            $olddsphase = $targatedModel->ds_phase;
            $targatedModel->ds_date = $validated['duaresarkarDate'];
            $targatedModel->ds_registration_no = $validated['ds_registration_no'];
            $targatedModel->ds_phase = $this->currentdsPhase;
            $targatedModel->application_type = 2;
            $targatedModel->save();
            $DsMapRecord = new DsMapRecord;
            $DsMapRecord->application_id = $this->applicantId;
            $DsMapRecord->new_ds_phase = $this->currentdsPhase;
            $DsMapRecord->new_ds_date = $validated['duaresarkarDate'];
            $DsMapRecord->new_ds_registration_no = $validated['ds_registration_no'];
            $DsMapRecord->old_ds_phase = $olddsphase;
            $DsMapRecord->old_ds_date = $olddsdate;
            $DsMapRecord->old_ds_registration_no = $olddsres;
            $DsMapRecord->save();
            $AcceptRejectInfo = new AcceptRejectInfo;
            $AcceptRejectInfo->application_id = $targatedModel->application_id;
            $AcceptRejectInfo->beneficiary_id = $targatedModel->beneficiary_id;
            $AcceptRejectInfo->ip_address = request()->ip();
            $AcceptRejectInfo->scheme_id = $targatedModel->scheme_id;
            $AcceptRejectInfo->user_id = Auth::id();
            $AcceptRejectInfo->browser = request()->header('User-Agent');
            $AcceptRejectInfo->model_name = null;
            $AcceptRejectInfo->op_type = 123;
            $AcceptRejectInfo->revert_reason_cause_id = null;
            $AcceptRejectInfo->revert_reason_remarks = null;
            $AcceptRejectInfo->parent_id = null;
            $AcceptRejectInfo->save();
            DB::commit();
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Application verified successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Something went wrong!',
            ]);
        }
        $this->dispatch('hide-modal');
        // $this->dispatch('refreshDatatable');
    }
    public function resetForm()
    {
        $this->reset(['ds_registration_no', 'duaresarkarDate']);
    }
    public function render()
    {
        return view('livewire.duplicate-applicant-d-s-mark-modal');
    }
}
