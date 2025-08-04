<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Support\Facades\Auth;
class SelfDeclaration extends Component
{
    public $mode;
    public $resident = true;
    public $no_govt_salary = false;
    public $info_true = true;
    public $aadhaar_consent = true;

    public function mount($mode = null)
    {
        $this->mode = $mode;
    }

    public function rules()
    {
        return [
            'resident' => 'accepted|boolean',
            'no_govt_salary' => 'nullable|boolean',
            'info_true' => 'accepted|boolean',
            'aadhaar_consent' => 'accepted|boolean',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        if ($this->mode === null) {
            $applicantion = DraftBeneficiaryPersonal::first();
            DraftBeneficiaryDeclaration::create([
                'application_id' => $applicantion->application_id,
                'is_resident' => $validated['resident'],
                'earn_monthly_remuneration' => $validated['no_govt_salary'],
                'info_genuine_decl' => $validated['info_true'],
                'av_status' => $validated['aadhaar_consent'],
                'created_by' => Auth::user()->id,
            ]);
        } else {
            
        }
    }

    public function render()
    {
        return view('livewire.self-declaration');
    }
}
