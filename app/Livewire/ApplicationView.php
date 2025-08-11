<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryPersonal;
use App\Models\DraftBeneficiaryPersonal;

class ApplicationView extends Component
{
    public $application;

    public function mount($id)
    {
        $reportType = request()->query('reportType');

        if ($reportType === 'verified') {
            $this->application = DraftBeneficiaryPersonal::with([
                'father',
                'contact.panchayat',
                'contact.ward',
                'casteName',
                'bank.ifscCodeMaster.bankMaster'
            ])->findOrFail($id);
        } elseif ($reportType === 'approved') {
             $this->application = BeneficiaryPersonal::with([
                'father',
                'contact.panchayat',
                'contact.ward',
                'casteName',
                'bank.ifscCodeMaster.bankMaster'
            ])->findOrFail($id);
        }       
    }
    public function render()
    {
        return view('livewire.application-view', [
            'application' => $this->application
        ]);
    }
}
