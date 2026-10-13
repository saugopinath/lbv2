<?php

namespace App\Livewire;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use App\Models\Scheme;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Services\WorkflowService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class FinalSubmitModal extends Component
{
    public $show = false;
    public $applicationId;
    public array $tabsData = [];
    public $previewTabCode = null;
    public $schemeId;
    public $schemeModuleId = null;
    public $schemeName;
    public $applicantPhoto;
    public $filter_data = [];

    protected $listeners = ['openFinalModal'];
    public function openFinalModal($applicationId, $tabsData, $schemeId = null, $schemeModuleId = null)
    {
        $this->applicationId = $applicationId;
        $this->tabsData = $tabsData;
        $this->schemeId = $schemeId;
        $this->schemeModuleId = $schemeModuleId;
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
        $this->schemeName = Scheme::where('id', $this->schemeId)->value('name');
    }
    public function close()
    {
        $this->show = false;
    }

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

        $BeneficiaryDetails = BeneficiaryPersonalDetail::select(
                'application_id',
                'beneficiary_id',
                'scheme_id',
                'scheme_module_id',
                'next_level_role_id',
                'is_final',
                'created_by_dist_code',
                'created_by_local_body_code'
            )
            ->where('application_id', $this->applicationId)
            ->where($this->filter_data)
            ->first();

        if (!$this->schemeModuleId && $BeneficiaryDetails && $BeneficiaryDetails->scheme_module_id) {
            $this->schemeModuleId = $BeneficiaryDetails->scheme_module_id;
        }

        $levelRoles = $workflowService->getLevelRoles($this->schemeId, null, $this->schemeModuleId);

        DB::beginTransaction();
        try {

            if ($BeneficiaryDetails) {
                $BeneficiaryDetails->next_level_role_id = $levelRoles ? $levelRoles->next_level_role_id : 20;
                if ($this->schemeModuleId) {
                    $BeneficiaryDetails->scheme_module_id = $this->schemeModuleId;
                }
                $BeneficiaryDetails->is_final = 1;
                $BeneficiaryDetails->updated_at = now();
                $BeneficiaryDetails->save();
            }

            $beneficiary_id = $BeneficiaryDetails?->beneficiary_id ?? BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->value('beneficiary_id');
            AcceptRejectInfo::create([
                'application_id'          => $this->applicationId,
                'beneficiary_id'          => $beneficiary_id,
                'ip_address'              => request()->ip(),
                'scheme_id'               => $this->schemeId,
                'user_id'                 => Auth::id(),
                'browser'                 => request()->header('User-Agent'),
                'model_name'              => null,
                'op_type'                 => Codemaster::getIdByCode(2101),
                'revert_reason_cause_id'  => null,
                'revert_reason_remarks'   => null,
                'parent_id'               => AcceptRejectInfo::where('application_id', $this->applicationId)
                    ->latest('id')
                    ->value('id'),
            ]);
            DB::commit();
            // $this->show = false;
            session()->flash('success', "Application ID: " . $this->applicationId . " Submitted successfully");
            return redirect()->route('form');
            $this->show = false;
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Please Configure Workflow Steps',
            ]);
            session()->flash('error', "Application ID: " . $this->applicationId . " Submitted failed!");
        }
    }

    public function render()
    {
        return view('livewire.final-submit-modal');
    }
}
