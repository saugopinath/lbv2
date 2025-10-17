<?php

namespace App\Livewire\CasteModification;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonal;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\CheckAuthHelper;
use Illuminate\Support\Facades\DB;

class CasteModificationAction extends Component
{
    public $applicationId;
    public $roleId;
    public $action;
    public $showModal = false;
    public $availableActions = [];
    public $heading = '';

    protected $rules = [
        'action' => 'required|string',
    ];

    public function mount($applicationId)
    {
        $this->applicationId = $applicationId;
        $this->roleId = CheckAuthHelper::getRoleId();

        // Define heading & actions dynamically
        if (CheckAuthHelper::isVerifier()) {
            $this->heading = "Process the Application : $this->applicationId";
            $this->availableActions = [
                '2202' => 'Verify',
                '2204' => 'Revert',
            ];
        } elseif (CheckAuthHelper::isApprover()) {
            $this->heading = "Process the Application : $this->applicationId";
            $this->availableActions = [
                '2203' => 'Approve',
                '2204' => 'Revert',
            ];
        }
    }

    public function openModal()
    {
        $this->reset('action');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset('action');
        // $this->resetErrorBag(); 
        $this->resetValidation();
        $this->showModal = false;
    }

    public function submit()
    {

        $this->validate();

        $casteModification = CasteModificationInfo::where('application_id', $this->applicationId)
            ->latest()
            ->first();

        if (!$casteModification) {
            session()->flash('error', 'Application not found!');
            return;
        }
        $mapping = [
            '2202' => Codemaster::getIdByCode(2202),
            '2203' => Codemaster::getIdByCode(2203),
            '2204' => Codemaster::getIdByCode(2204),
        ];
        $opTypeMapping = [
            '2202' => Codemaster::getIdByCode(2107), // Verify op_type
            '2203' => Codemaster::getIdByCode(2108), // Approve op_type
            '2204' => Codemaster::getIdByCode(2109), // Revert op_type
        ];
        if (!isset($mapping[$this->action])) {
            session()->flash('error', 'Invalid action selected!');
            return;
        }
        $previousId = AcceptRejectInfo::where('application_id', $this->applicationId)
            ->orderByDesc('id')
            ->value('id');
        try {
            DB::beginTransaction();

            $casteModification->next_level_requested_id = $mapping[$this->action];
            $casteModification->updated_by              = Auth::id();
            // $casteModification->is_active              = false;
            $casteSaved = $casteModification->save();
            // dump($casteSaved);

            $acceptReject = new AcceptRejectInfo();
            $acceptReject->application_id         = $this->applicationId;
            $acceptReject->beneficiary_id         = $casteModification->beneficiary_id;
            $acceptReject->ip_address             = request()->ip();
            $acceptReject->user_id                = Auth::id();
            $acceptReject->browser                = request()->header('User-Agent');
            $acceptReject->model_name             = class_basename(static::class) . '@' . __FUNCTION__;
            $acceptReject->op_type                = $opTypeMapping[$this->action];
            $acceptReject->revert_reason_cause_id = null;
            $acceptReject->revert_reason_remarks  = null;
            $acceptReject->parent_id              = $previousId;
            $acceptSaved = $acceptReject->save();
            // dump($acceptSaved);
            $beneficiarySaved = true;
            $casteUpdated = true;

            if ($this->action == '2203') {
                // Update Beneficiary
                $beneficiary = BeneficiaryPersonal::where('application_id', $this->applicationId)->first();
                if ($beneficiary) {
                    $beneficiary->caste = $casteModification->new_data['caste'];
                    $beneficiary->caste_certificate_no = $casteModification->new_data['caste_certificate_no'];
                    $beneficiarySaved = $beneficiary->save();
                } else {
                    $beneficiarySaved = false;
                }

                // Update CasteModification
                $casteModification->is_active = 0;
                $casteModification->updated_by = Auth::id();
                $casteUpdated = $casteModification->save();
            }
            // dd($beneficiarySaved);

            // dump($casteSaved);
            // dump($acceptSaved);
            // dd($beneficiarySaved);
            // dd($casteUpdated);
            // dd($casteSaved && $acceptSaved && $beneficiarySaved);
            if ($casteSaved && $acceptSaved && $beneficiarySaved) {
                DB::commit();
                session()->flash('success', "Application Processed successfully!");
                return redirect()->route('caste-modification-list');
            } else {
                DB::rollBack();
                session()->flash('error', 'Transaction failed. Some records were not saved.');
                return;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong: ' . $e->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.caste-modification.caste-modification-action');
    }
}
