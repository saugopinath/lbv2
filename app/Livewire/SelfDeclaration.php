<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Support\Facades\Auth;

class SelfDeclaration extends Component
{
    public $mode, $id;
    public $resident = false;
    public $no_govt_salary = false;
    public $info_true = false;
    public $aadhaar_consent = false;

    public function mount($mode = null, $id = null)
    {
        $this->mode = $mode;
        if ($id != null) {
            $this->id = $id;
            $app_det = DraftBeneficiaryPersonal::with('declaration')->where('application_id', $id)->first();
            $this->resident = $app_det->declaration->is_resident;
            $this->no_govt_salary = $app_det->declaration->earn_monthly_remuneration;
            $this->info_true = $app_det->declaration->info_genuine_decl;
            $this->aadhaar_consent = $app_det->declaration->av_status;
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
        $validated = $this->validate();
        if ($this->mode === null) {
            $applicantion = DraftBeneficiaryPersonal::first();
            DraftBeneficiaryDeclaration::create([
                'application_id' => $applicantion->application_id,
                'is_resident' => $validated['resident'],
                'earn_monthly_remuneration' => $validated['no_govt_salary'],
                'info_genuine_decl' => $validated['info_true'],
                'av_status' => $validated['aadhaar_consent'],
                'created_by' => Auth::id(),
            ]);
        } else {
            $data = [
                'is_resident' => $validated['resident'],
                'earn_monthly_remuneration' => $validated['no_govt_salary'],
                'info_genuine_decl' => $validated['info_true'],
                'av_status' => $validated['aadhaar_consent'],
            ];
            DraftBeneficiaryDeclaration::where('application_id', $this->id)->update($data);
        }
    }

    public function render()
    {
        return view('livewire.self-declaration');
    }
}
