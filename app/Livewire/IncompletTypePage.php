<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ApplicantIncompletDeatil;

class IncompletTypePage extends Component
{
    public $id;
    public $page;
    public $applicantInfo; 

    public function mount($id)
    {
        $this->id = $id;

        $this->page = ApplicantIncompletDeatil::where('application_id', $id)
            ->with([
                'incompletType',
                'beneficiaryCommonList.beneficiaryPersonal.father',
                'beneficiaryCommonList.panchayat',
                'beneficiaryCommonList.ward',
            ])->get();

        $this->applicantInfo = $this->page->first()?->beneficiaryCommonList;
    }

    public function submit()
    {
        dd('ok');
    }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'page' => $this->page,
            'applicantInfo' => $this->applicantInfo,
        ]);
    }
}
