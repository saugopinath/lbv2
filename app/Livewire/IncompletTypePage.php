<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\Crypt;
use App\Models\ApplicantIncompletDeatil;

class IncompletTypePage extends Component
{
    public $id;
    public $page;
    public $stage;
    public $applicantInfo;
    public $formData = [];
    public $revertReasons = [];
    public $user_id;
    public $revert_reason_cause_id;
    public $revert_reason_remarks;

    // ✅ New properties for issues
    public $aadhaarIssues = [];
    public $mobileIssues = [];
    public $sortedBankIssues = [];
    public $ifscode, $new_bank_account;
    public $bank_action;

    protected $listeners = ['trigger-update' => 'recivedupdateddata'];

    public function mount($id)
    {
        $this->stage = request()->query('stage');
        $this->id = $id;

        $select_lgd = session('lgd_session');
        $this->user_id = Crypt::decryptString($select_lgd['role_id']);

        $this->revertReasons = Codemaster::where('parent_id', Codemaster::getIdByCode(12))->get();

        $this->page = ApplicantIncompletDeatil::where('application_id', $id)
            ->with([
                'incompletType',
                'beneficiaryCommonList.enclosures',
                'beneficiaryCommonList.aadhaar',
                'beneficiaryCommonList.bank',
                'beneficiaryCommonList.beneficiaryPersonal.father',
                'beneficiaryCommonList.panchayat',
                'beneficiaryCommonList.ward',
            ])->get();

        $this->applicantInfo = $this->page->first()?->beneficiaryCommonList;

        // ✅ Do classification here
        $this->classifyIssues();
    }
    public function recivedupdateddata($data)
    {
        $this->ifscode = $data['ifscode'];
        $this->new_bank_account = $data['new_bank_account'];
        $this->bank_action = $data['bank_action'];
    }

    public function submit()
    {
        // dd('ok');
        $request = AcceptRejectInfo::create([
            'application_id'         => $this->id,
            'beneficiary_id'         => $this->applicantInfo->beneficiary_id ?? null,
            'ip_address'             => request()->ip(),
            'user_id'                => $this->user_id,
            'browser'                => request()->header('User-Agent'),
            'model_name'             => 'ApplicantIncompleteDetail',
            'op_type'                => Codemaster::where('code', 245)->value('id'),
            'revert_reason_cause_id' => null,
            'revert_reason_remarks'  => null,
            'parent_id'              => null,
        ]);
        $jsonValue = [
            'ifscode'          => $this->ifscode,
            'new_bank_account' => $this->new_bank_account,
            'application_id' => $this->id,
        ];
        // $jsonValue1 = json_encode($jsonValue);
        // dd( $jsonValue1);

        $item = $this->page->first();
        if ($item) {
            $item->update([
                'new_value' => $jsonValue,
                'change_type' => $this->bank_action,
                'next_level_request_id' => 1,
                'request_id'            => $request->id,
            ]);
        }
             session()->flash('success', 'Incomplete details updated successfully!');
        return redirect()->route('incomplete.types', ['stage' => 'verifier','id' => $this->id]);
    }

    private function classifyIssues()
    {
        $aadhaarIssues = [];
        $mobileIssues = [];
        $bankIssues = [];

        $bankPriority = [
            'DUPLICATE BANK ACCOUNT NUMBER',
            'NAME VALIDATION  FAILED IN BANK',
            'ACCOUNT NUMBER VALIDATION  FAILED IN BANK',
            'MINOR MISMATCH(40% - 89%)',
            'MINOR MISMATCH(90% - 100%)',
        ];

        foreach ($this->page as $item) {
            $typeName = $item->incompletType->name;

            if (in_array($typeName, ['PDS MISMATCH', 'NO AADHAR NUMBER', 'DUPLICATE AADHAR NUMBER'])) {
                $aadhaarIssues[] = $item;
            } elseif (in_array($typeName, ['NO MOBILE NUMBER', 'DUPLICATE MOBILE NUMBER'])) {
                $mobileIssues[] = $item;
            } elseif (in_array($typeName, $bankPriority)) {
                $bankIssues[] = $item;
            }
        }

        $this->aadhaarIssues = $aadhaarIssues;
        $this->mobileIssues = $mobileIssues;

        // ✅ Sort bank issues
        $this->sortedBankIssues = collect($bankIssues)->sortBy(
            fn($item) => array_search($item->incompletType->name, $bankPriority)
        )->values();
    }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'applicantInfo' => $this->applicantInfo,
            'aadhaarIssues' => $this->aadhaarIssues,
            'mobileIssues' => $this->mobileIssues,
            'sortedBankIssues' => $this->sortedBankIssues,
            'stage' => $this->stage,
            'revertReasons' => $this->revertReasons,
            'id' => $this->id,
        ]);
    }
}
