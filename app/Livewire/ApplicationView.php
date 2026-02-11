<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryPersonal;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\SchemeTabMapping;
use App\Services\ApplicationTabService;

class ApplicationView extends Component
{
    public $application;
    public $reportType;
    public $label;
    public $value;
    public $applicationId;
    public $schemeId;
    public $is_duplicate;
    public $SchemeRelatedTab;
    public $tabs = [];
    protected ApplicationTabService $tabService;
    public function mount($id, $is_duplicate = 0, $schemeId = null)
    {
        // dd('ok');
        $this->applicationId = $id;
        $this->schemeId = $schemeId;
        // $decrypted = Crypt::decryptString($id);
        // $this->schemeId = request()->query('schemeId');
        // $this->reportType = request()->query('reportType');
        $this->is_duplicate   = $is_duplicate;
        // dd($realId, $this->schemeId, $this->reportType, $this->is_duplicate);
        // dd( $this->reportType);
        // if ($this->reportType === '3') {
        //     // $this->application = BeneficiaryPersonal::findOrFail($realId);
        //     $this->application = BeneficiaryPersonal::where('application_id', $realId)->first();
        //     //  dd($this->application);
        //     $this->label = 'Beneficiary Id';
        //     $this->value = $this->application->beneficiary_id;
        //     $this->passId = $this->application->application_id;
        // } else {
        //     // $this->application = DraftBeneficiaryPersonal::findOrFail($realId);
        //     $this->application = DraftBeneficiaryPersonal::where('application_id', $realId)->first();
        //     // dd( $this->application );
        //     $this->label = 'Application Id';
        //     $this->value = $this->application->application_id;
        //     $this->passId = $this->application->application_id;
        // }
        // $this->SchemeRelatedTab = SchemeTabMapping::where('scheme_id', $this->schemeId)->pluck('tab_id');
        // dd($this->SchemeRelatedTab);
        $this->tabs = $this->tabService
            ->getTabs($this->schemeId, $this->applicationId);
    }
    public function boot(ApplicationTabService $tabService)
    {
        $this->tabService = $tabService;
    }

    public function render()
    {
        return view('livewire.application-view');
    }
}
