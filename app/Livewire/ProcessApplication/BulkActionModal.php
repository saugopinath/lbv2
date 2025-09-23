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
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\Crypt;

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

        $validated = $this->validate([
            'bulkActionType' => 'required|in:V,A,R,T',
            'reason' => in_array($this->bulkActionType, ['R', 'T']) ? 'required' : 'nullable',
            'remark' => in_array($this->bulkActionType, ['R', 'T']) ? 'required|string|max:255' :'nullable',
        ]);

        $successMessage = 'Action performed successfully!';
        $approverRoleId = Codemaster::getIdByCode(23);
        $operatorRoleId = Codemaster::getIdByCode(21);
        $currentUserId = Auth::id();
        $select_lgd = session('lgd_session');
        $user_id = Crypt::decryptString($select_lgd['role_id']);


        // DB::transaction(function () use (&$successMessage, $approverRoleId, $operatorRoleId, $currentUserId) {
        $ids = $this->selectedRows;
        if ($this->bulkActionType === 'V') {
            foreach ($ids as $id) {
                DraftBeneficiaryPersonal::where('application_id', $id)
                    ->update(['next_level_role_id' => $approverRoleId]);
                $beneficiary = DraftBeneficiaryPersonal::where('application_id', $id)->first();
                AcceptRejectInfo::Create(
                    [
                        'application_id' => $beneficiary->application_id,
                        'beneficiary_id' => $beneficiary->application_id,
                        'ip_address'     => request()->ip(),
                        'user_id'        => Auth::id(),
                        'browser'        => request()->header('User-Agent'),
                        'model_name'     => null,
                        'op_type'        => Codemaster::getIdByCode(2302),
                        'revert_reason_cause_id' => null,
                        'revert_reason_remarks'  => null,
                        'parent_id'      => AcceptRejectInfo::where('application_id', $id)
                            ->latest('id')
                            ->value('id') ?? null,
                    ]
                );
            }
        } elseif ($this->bulkActionType === 'A') {
            foreach ($ids as $id) {
                $draft = DraftBeneficiaryPersonal::where('application_id', $id)->first();
                if ($draft) {
                    $draft->delete();
                }
                $select_lgd = session('lgd_session');
                AcceptRejectInfo::Create(
                    [
                        'application_id' => $draft->application_id,
                        'beneficiary_id' => $draft->application_id,
                        'ip_address'     => request()->ip(),
                        'user_id'        => Crypt::decryptString($select_lgd['role_id']),
                        'browser'        => request()->header('User-Agent'),
                        'model_name'     => null,
                        'op_type'        => Codemaster::getIdByCode(2303),
                        'revert_reason_cause_id' => null,
                        'revert_reason_remarks'  => null,
                        'parent_id'      => AcceptRejectInfo::where('application_id', $draft->application_id)
                            ->latest('id')
                            ->value('id') ?? null,
                    ]
                );
            }
        } elseif ($this->bulkActionType === 'T') {

            $user = auth()->user();
            if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
                $next_level_role_id = Codemaster::getIdByCode(22);
            }
            if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
                $next_level_role_id = Codemaster::getIdByCode(21);
            }
            foreach ($ids as $id) {
                DraftBeneficiaryPersonal::where('application_id', $id)
                    ->update(['next_level_role_id' => $next_level_role_id]);
                $beneficiary = DraftBeneficiaryPersonal::where('application_id', $id)->first();
                AcceptRejectInfo::Create(
                    [
                        'application_id' => $beneficiary->application_id,
                        'beneficiary_id' => $beneficiary->application_id,
                        'ip_address'     => request()->ip(),
                        'user_id'        => $user_id,
                        'browser'        => request()->header('User-Agent'),
                        'model_name'     => null,
                        'op_type'        => Codemaster::getIdByCode(2304),
                        'revert_reason_cause_id' => $validated['reason'],
                        'revert_reason_remarks'  => $validated['remark'],
                        'parent_id'      => AcceptRejectInfo::where('application_id', $id)
                            ->latest('id')
                            ->value('id') ?? null,
                    ]
                );
            }
        } elseif ($this->bulkActionType === 'R') {
            foreach ($ids as $id) {
                $benrej = new BenRejectDetails;
                $benrej->application_id     = $id;
                $benrej->created_by     = $user_id;
                $benrej->district_id     = Crypt::decryptString($select_lgd['district_id']);
                $benrej->personal_details     = DraftBeneficiaryPersonal::where('application_id', $id)->get()->toArray();
                $benrej->contact_details      = DraftBeneficiaryContact::where('application_id', $id)->get()->toArray();
                $benrej->bank_details         = DraftBeneficiaryBank::where('application_id', $id)->get()->toArray();
                $benrej->declaration_details  = DraftBeneficiaryDeclaration::where('application_id', $id)->get()->toArray();
                $benrej->relationship_details = DraftBeneficiaryRelationship::where('application_id', $id)->get()->toArray();
                $benrej->aadhar_details       = BeneficiaryAadhaar::where('application_id', $id)->get()->toArray();
                $benRejectDetails = $benrej->save();
                AcceptRejectInfo::Create(
                    [
                        'application_id' => $id,
                        'beneficiary_id' => $id,
                        'ip_address'     => request()->ip(),
                        'user_id'        => $user_id,
                        'browser'        => request()->header('User-Agent'),
                        'model_name'     => null,
                        'op_type'        => Codemaster::getIdByCode(2305),
                        'revert_reason_cause_id' => $validated['reason'],
                        'revert_reason_remarks'  => $validated['remark'],
                        'parent_id'      => AcceptRejectInfo::where('application_id', $id)
                            ->latest('id')
                            ->value('id') ?? null,
                    ]
                );
            }
        }
        // });
        // Toaster::success($successMessage);

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
