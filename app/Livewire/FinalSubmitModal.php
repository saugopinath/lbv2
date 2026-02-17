<?php

namespace App\Livewire;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Scheme;
use Exception;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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
    public $filter_data = [];

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

    // public function confirmSubmit(WorkflowService $workflowService)
    // {
    //     $labelRoles = $workflowService->getLabelRoles($this->schemeId);
    
    //     try {
    //         BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->update([
    //             'next_level_role_id' => $labelRoles->next_label_role_id,
    //             'is_final' => 1,
    //         ]);
    //         // $this->show = false;
    //         session()->flash('success', "Application ID: " . $this->applicationId . " Submitted successfully");
    //         return redirect()->route('schemes.final-submitted');
    //         $this->show = false;
    //     } catch (Exception $e) {
    //         dd($e);
    //         session()->flash('error', "Application ID: " . $this->applicationId . " Submitted failed!");
    //     }
    // }
    public function confirmSubmit(WorkflowService $workflowService)
    {
        $select_lgd = session('lgd_session');
        // dd($select_lgd);
        if (!empty($select_lgd['district_id'])) {
            $this->filter_data['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_data['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_data['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
        $labelRoles = $workflowService->getLabelRoles($this->schemeId);

        try {
            BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->where($this->filter_data)->update([
                'next_level_role_id' => $labelRoles->next_label_role_id,
                'is_final' => 1,
                'updated_at' => now(),
            ]);
            $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->value('beneficiary_id');
            $AcceptRejectInfo = new AcceptRejectInfo();
            $AcceptRejectInfo->application_id = $this->applicationId;
            $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
            $AcceptRejectInfo->ip_address = request()->ip();
            $AcceptRejectInfo->scheme_id = $this->schemeId;
            $AcceptRejectInfo->user_id = Auth::id();
            $AcceptRejectInfo->browser = request()->header('User-Agent');
            $AcceptRejectInfo->model_name = null;
            $AcceptRejectInfo->op_type = 1;
            $AcceptRejectInfo->revert_reason_cause_id = null;
            $AcceptRejectInfo->revert_reason_remarks = null;
            $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $this->applicationId)
                ->latest('id')
                ->value('id') ?? null;
            $AcceptRejectInfo->save();

            // $this->show = false;
            session()->flash('success', "Application ID: " . $this->applicationId . " Submitted successfully");
            return redirect()->route('schemes.final-submitted');
            $this->show = false;
        } catch (Exception $e) {
            dd($e);
            session()->flash('error', "Application ID: " . $this->applicationId . " Submitted failed!");
        }
    }

    public function render()
    {
        return view('livewire.final-submit-modal');
    }
}
