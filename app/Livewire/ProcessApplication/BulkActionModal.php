<?php

namespace App\Livewire\ProcessApplication;
use App\Models\FaultyBeneficiaryPersonal;
use Livewire\Component;
use App\Models\Codemaster;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;
use App\Models\BenRejectDetails;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\ApplicantRejectRevertDetails;
use App\Models\DraftBeneficiaryRelationship;

class BulkActionModal extends Component
{
    public bool $bulkActionModal = false;
    public array $selectedRows = [];
    public string $bulkActionType = '';
    public ?int $reason = null;

    public string $remark = '';
    public array $reasons = [];
    public array $availableActions = [];
    public int $currentUserId;

    public string $bulkActionTypeLabel = 'Select Operation';

    #[On('openBulkActionModal')]
    public function openModal(array $selectedIds = [])
    {
        $this->reset(['bulkActionType', 'reason', 'remark', 'availableActions', 'bulkActionTypeLabel']);
        $this->selectedRows = $selectedIds;
        // dd($this->selectedRows);

        $user = Auth::user();
        // dump($user);


        if ($user->hasRole(['Verifier', 'Delegated Verifier'])) {

            $this->availableActions = [
                'V' => 'Verify',
                'R' => 'Reject',
                'T' => 'Revert',
            ];
        } elseif ($user->hasRole(['Approver', 'Delegated Approver'])) {

            $this->availableActions = [
                'A' => 'Approve',
                'R' => 'Reject',
                'T' => 'Revert',
            ];
        }


        $this->bulkActionModal = true; //render again
    }

    public function updatedBulkActionType($value)
    {
        if (in_array($value, ['R', 'T'])) {
            $this->reasons = Codemaster::where('parent_id', 12)
                ->orderBy('id', 'asc')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $this->reasons = [];
            $this->reason = null;
            $this->remark = '';
        }
    }

    public function performBulkAction()
    {
        $this->validate([
            'bulkActionType' => 'required|in:V,A,R,T',
            'reason' => in_array($this->bulkActionType, ['R', 'T']) ? 'required' : 'nullable',
            'remark' => 'nullable|string|max:255',
        ]);

        $successMessage = 'Action performed successfully!';
        $approverRoleId = Codemaster::getIdByCode(23);
        $operatorRoleId = Codemaster::getIdByCode(21);
        $currentUserId = Auth::id();



        DB::transaction(function () use (&$successMessage, $approverRoleId, $operatorRoleId, $currentUserId) {


            if ($this->bulkActionType === 'V') {
                DraftBeneficiaryPersonal::whereIn('application_id', $this->selectedRows)
                    ->update(['next_level_role_id' => $approverRoleId]);


                $successMessage = "The selected application(s) have been successfully verified.";

            } elseif ($this->bulkActionType === 'A') {
                DraftBeneficiaryPersonal::whereIn('application_id', $this->selectedRows)
                    ->update(['next_level_role_id' => '24']);
                foreach ($this->selectedRows as $applicationId) {

                    BeneficiaryPersonal::create([
                        'application_id' => $applicationId,
                        'created_by' => $currentUserId,
                    ]);

                    $successMessage = "The selected application(s) have been successfully approved.";

                }
            } elseif ($this->bulkActionType === 'T') {
                foreach ($this->selectedRows as $applicationId) {

                    DraftBeneficiaryPersonal::where('application_id', $applicationId)
                        ->update(['next_level_role_id' => $operatorRoleId]);

                    ApplicantRejectRevertDetails::create([
                        'application_id' => $applicationId,
                        'created_by' => $currentUserId,
                        'reject_revert_reason_id' => $this->reason,
                        'remark' => $this->remark,
                        'action_type' => $this->bulkActionType, // T for Revert
                    ]);
                }

                $successMessage = "The selected application(s) have been successfully reverted.";

            } elseif ($this->bulkActionType === 'R') {
                foreach ($this->selectedRows as $applicationId) {
                    // dd([
                    //     'application_id' => $applicationId,
                    //     'reject_revert_reason_id' => $this->reason,
                    //     'remark' => $this->remark,
                    // ]);
                    ApplicantRejectRevertDetails::create([
                        'application_id' => $applicationId,
                        'created_by' => $currentUserId,
                        'reject_revert_reason_id' => $this->reason,
                        'remark' => $this->remark,
                        'action_type' => $this->bulkActionType,
                    ]);




                    $reject = new BenRejectDetails([
                        'application_id' => $applicationId,
                    ]);
                    $reject->update_code = 1;// for draft table
                    $reject->save();



                }


                $successMessage = "The selected application(s) have been successfully rejected.";

            }
        });
        Toaster::success($successMessage);

        $this->bulkActionModal = false;

        $this->reset(['bulkActionType', 'reason', 'remark', 'selectedRows', 'bulkActionTypeLabel']);

        return redirect()->route('submitted-list');
        // $this->dispatch('toaster-success', $successMessage);
        // $this->dispatch('actionPerformedAndRedirect');
    }



    public function render()
    {
        return view('livewire.process-application.bulk-action-modal');
    }
}