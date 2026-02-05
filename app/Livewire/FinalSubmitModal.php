<?php

namespace App\Livewire;

use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Scheme;
use Exception;
use Livewire\Component;

class FinalSubmitModal extends Component
{
    public $show = false;
    public $applicationId;
    public array $tabsData = [];
    public $previewTabCode = null;
    public $schemeId;
    public $schemeName;
    public $applicantPhoto;

    protected $listeners = ['openFinalModal'];
    public function openFinalModal($applicationId, $tabsData, $schemeId = null)
    {
        $this->applicationId = $applicationId;
        $this->tabsData = $tabsData;       
        $this->schemeId = $schemeId;
        $this->loadimage();
        $this->loadSchemeName();
        $this->show = true;
    }

    protected function loadimage()
    {
        $photo = BeneficiaryEnclosure::where('application_id', $this->applicationId)
            ->where('document_type', 103)
            ->value('attched_document');
        if (!$photo) {
            $this->applicantPhoto = asset('images/default-user.png');
            return;
        }
        if (str_contains($photo, 'data:image')) {

            $this->applicantPhoto = $photo;
        } elseif (base64_decode($photo, true)) {
            $this->applicantPhoto = 'data:image/jpeg;base64,' . $photo;
        } else {
            $this->applicantPhoto = asset('storage/' . $photo);
        }
    }
    public function loadSchemeName()
    {
        $scheme = Scheme::find($this->schemeId);
        $this->schemeName = $scheme->name;
    }
    public function close()
    {
        $this->show = false;
    }

    public function confirmSubmit()
    {
        try {
            BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->update([
                'next_level_role_id' => 1,
            ]);
            // $this->show = false;
            session()->flash('success', "Application ID: " . $this->applicationId . " Submitted successfully");
            return redirect()->route('schemes.final-submitted');
            $this->show = false;
        } catch (Exception $e) {
            session()->flash('error', "Application ID: " . $this->applicationId . " Submitted failed!");
        }
    }

    public function render()
    {
        return view('livewire.final-submit-modal');
    }
}