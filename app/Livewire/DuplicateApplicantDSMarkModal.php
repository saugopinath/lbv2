<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\DsPhase;
use App\Models\BeneficiaryCommonList;
use App\Models\DsMapRecord;
use App\Models\Codemaster;
class DuplicateApplicantDSMarkModal extends Component
{
    public $applicantId, $open;
    public $cdate, $pdate, $reg_no, $ds_date, $cdsphase;
    #[On('opendsMarkModal')]
    public function openModal($id = null)
    {
        $this->applicantId = $id;
        $this->dispatch('show-modal');
        $this->cdate = Carbon::now()->format('Y-m-d');
        $this->pdate = DsPhase::where('is_current', true)->value('base_dob');
        $this->cdsphase = DsPhase::where('is_current', true)->value('phase_code');
    }
    public function mount() {}
    public function rules()
    {
        $rules = [
            'reg_no'   => 'required',
            'ds_date'   => "required|date",
        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'reg_no.*'     => 'Registration number is required.',
            'ds_date.*'    => 'DS date is required.',
        ];
    }
    public function saveDsMark()
    {
        $validated = $this->validate($this->rules());
        $targatedModel = BeneficiaryCommonList::find($this->applicantId)->sourceable;
        $olddsres = $targatedModel->ds_registration_no;
        $olddsdate = $targatedModel->ds_date;
        $olddsphase = $targatedModel->ds_phase;
        $targatedModel->ds_date = $validated['ds_date'];
        $targatedModel->ds_registration_no = $validated['reg_no'];
        $targatedModel->ds_phase = $this->cdsphase;
        $targatedModel->entry_type = Codemaster::getIdByCode(42);
        $targatedModel->save();
        $DsMapRecord = new DsMapRecord;
        $DsMapRecord->application_id = $this->applicantId;
        $DsMapRecord->new_ds_phase = $this->cdsphase;
        $DsMapRecord->new_ds_date = $validated['ds_date'];
        $DsMapRecord->new_ds_registration_no =$validated['reg_no'];
        $DsMapRecord->old_ds_phase = $olddsphase;
        $DsMapRecord->old_ds_date = $olddsdate;
        $DsMapRecord->old_ds_registration_no = $olddsres;
        $DsMapRecord->save();
        $this->dispatch('hide-modal');
    }
    public function resetForm()
    {
        $this->reset(['reg_no', 'ds_date']);
    }
    public function render()
    {
        return view('livewire.duplicate-applicant-d-s-mark-modal');
    }
}
