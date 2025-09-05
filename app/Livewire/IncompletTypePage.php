<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ApplicantIncompletDeatil;

class IncompletTypePage extends Component
{
    public $id;
    public $page;
    public $applicantInfo;
    public $formData = [];

    public function mount($id)
    {
        $this->id = $id;

        $this->page = ApplicantIncompletDeatil::where('application_id', $id)
            ->with([
                'incompletType',
                'beneficiaryCommonList.enclosures',
                'beneficiaryCommonList.beneficiaryBank',
                'beneficiaryCommonList.beneficiaryPersonal.father',
                'beneficiaryCommonList.panchayat',
                'beneficiaryCommonList.ward',
            ])->get();

        $this->applicantInfo = $this->page->first()?->beneficiaryCommonList;
    }

    public function submit()
    {
        foreach ($this->page as $item) {
            $type = $item->incompletType->name ?? null;

            if (!$type) {
                continue;
            }

            $newValue = null;

            $map = [
                'NO AADHAR NUMBER' => 'aadhar',
                'DUPLICATE AADHAR NUMBER' => 'new_aadhar',
                'DUPLICATE BANK ACCOUNT NUMBER' => 'new_bank_account',
                'NO MOBILE NUMBER' => 'mobile',
                'NAME VALIDATION  FAILED IN BANK' => 'bank_name',
                'ACCOUNT NUMBER VALIDATION  FAILED IN BANK' => 'bank_account',
                'DUPLICATE MOBILE NUMBER' => 'new_mobile',
                'MINOR MISMATCH(40% - 89%)' => 'mismatch_low',
                'MINOR MISMATCH(90% - 100%)' => 'mismatch_high',
                'PDS MISMATCH' => 'pds',
            ];

            if (isset($map[$type]) && isset($this->formData[$map[$type]][$item->id])) {
                $newValue = $this->formData[$map[$type]][$item->id];
            }

            if ($newValue) {
                $item->new_value = $newValue;
                $item->next_level_request_id = 1; 
                $item->save();
            }
        }

        session()->flash('success', 'Incomplete details updated successfully!');
        return redirect()->route('incomplete.types', ['id' => $this->id]);
    }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'page' => $this->page,
            'applicantInfo' => $this->applicantInfo,
        ]);
    }
}
