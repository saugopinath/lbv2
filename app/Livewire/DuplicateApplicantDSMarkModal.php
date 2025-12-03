<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\DsPhase;

class DuplicateApplicantDSMarkModal extends Component
{
    public $applicantId, $open;
    public $cdate, $pdate, $reg_no, $ds_date;
    #[On('opendsMarkModal')]
    public function openModal($id = null)
    {
        $this->applicantId = $id;
        $this->dispatch('show-modal');
        $this->cdate = Carbon::now()->format('Y-m-d');
        $this->pdate = DsPhase::where('is_current', true)->value('base_dob');
        // $this->ds_date = $this->cdate;
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
