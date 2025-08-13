<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryEnclosure;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;

class ApplicationView extends Component
{
    public $ffname;
    public $mfname;
    public $application;
    public $decryptedData = null;
    public $showModal = false;

    public function mount($id)
    {
        $reportType = request()->query('reportType');

        if ($reportType === '2') {
            $this->application = DraftBeneficiaryPersonal::with([
                // 'contact.panchayat',
                // 'contact.ward',
                // 'casteName',
                'bank.ifscCodeMaster.bankMaster',
                'beneficiaryEnclosures.documentType'
            ])->findOrFail($id);
            // dd( $this->application);
            $this->ffname = optional(
                $this->application->father->firstWhere('relation_type_id', Codemaster::getIdByCode(131))
            )->full_name;

            $this->mfname = optional(
                $this->application->father->firstWhere('relation_type_id', Codemaster::getIdByCode(132))
            )->full_name;
        } elseif ($reportType === '3') {
            $this->application = BeneficiaryPersonal::with([
                // 'contact.panchayat',
                // 'contact.ward',
                // 'casteName',
                'bank.ifscCodeMaster.bankMaster'
            ])->findOrFail($id);

            $this->ffname = optional(
                $this->application->father->firstWhere('relation_type_id', Codemaster::getIdByCode(131))
            )->full_name;

            $this->mfname = optional(
                $this->application->father->firstWhere('relation_type_id', Codemaster::getIdByCode(132))
            )->full_name;
        }
    }

    public function viewDocument($id)
    {
        $enclosure = BeneficiaryEnclosure::findOrFail($id);           

        // try {
        //     $this->decryptedData = Crypt::decrypt($enclosure->attached_document);
        //     $this->showModal = true; 
        // } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        //     $this->decryptedData = "Decryption failed or data corrupted.";
        //     $this->showModal = true;
        // }
    }

    public function closeView()
    {
        $this->decryptedData = null;
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.application-view');
    }
}
