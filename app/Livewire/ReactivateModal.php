<?php

namespace App\Livewire;

use App\Models\AcceptRejectInfo;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BeneficiaryEnclosure;
use App\Models\Codemaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ReactivateModal extends Component
{
    public $applicantId;
    public $beneficiary_id, $jnmp_name, $dod, $name, $gender, $mobile, $father_name;
    public $dob, $aadhaar_no;
    public $reactive_reason;
    public $revert_reason_cause_id, $revert_reason_remarks;

    #[On('showReactivateModal')]
    public function openModal($id)
    {
        $this->applicantId = $id;
        $this->loadBeneficiaryData();
        $this->dispatch('show-modal');
    }
    public function mount()
    {
        $this->reactive_reason = Codemaster::where('parent_id', Codemaster::getIdByCode(211))->get();
    }
    public function loadBeneficiaryData()
    {
        $record = BeneficiaryPersonalDetail::with(['contact', 'mapping', 'jnmp'])
            ->where('application_id', $this->applicantId)
            ->where('jnmp_marked', 1)
            ->whereHas('mapping', function ($q) {
                $q->where('payment_suspend', 1);
            })
            ->first();

        if (!$record) {
            return;
        }

        $b = $record;

        $this->beneficiary_id = $b->beneficiary_id;
        $this->name = trim($b->beneficiary_name);
        $this->mobile = $b->mobile_no;
        $this->dob = $b->dob ? date('d-m-Y', strtotime($b->dob)) : '—';
        $this->aadhaar_no = $b->aadhaar_no ?? null;

        $this->father_name = $b->ben_father_name ?? 'N/A';

        // JNMP Data
        if ($record->jnmp) {
            $this->jnmp_name = $record->jnmp->deceasedfullname;
            $this->dod = $record->jnmp->dateofdeath;
            $this->gender = $record->jnmp->genderdesc;
        }
    }
    public function rules()
    {
        return [
            'revert_reason_cause_id' => 'required',
            'revert_reason_remarks' => 'required|max:100',
        ];
    }
    public function saveDsMark()
    {
        $this->validate([
            'revert_reason_cause_id' => 'required',
            'revert_reason_remarks' => 'required|max:100',
        ]);

        $uploadedDocsCount = BeneficiaryEnclosure::where('application_id', $this->applicantId)
            ->whereIn('document_type', [153])
            ->count();

        if ($uploadedDocsCount < 1) {
            $this->addError('document_upload', 'Please upload the required document.');
            return;
        }

        DB::beginTransaction();

        try {

            $ben = BeneficiaryPersonalDetail::with(['contact', 'mapping', 'jnmp'])
                ->where('application_id', $this->applicantId)
                ->firstOrFail();

            $personal = $ben;
            $mapping = $personal->mapping;

            if ($mapping) {
                $mapping->payment_suspend = null;
                $mapping->save();
            }

            $personal->jnmp_remarks = $this->revert_reason_remarks;
            $personal->reactive_reason = $this->revert_reason_cause_id;
            $personal->save();

            AcceptRejectInfo::create([
                'beneficiary_id' => $personal->beneficiary_id,
                'application_id' => $this->applicantId,
                'scheme_id' => 20,
                'ip_address' => request()->ip(),
                'user_id' => Auth::id(),
                'browser' => request()->header('User-Agent'),
                'model_name' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod(),
                'op_type' => $personal->next_level_role_id,
                'revert_reason_cause_id' => $this->revert_reason_cause_id,
                'revert_reason_remarks' => $this->revert_reason_remarks,
                'parent_id' => null,
            ]);

            DB::commit();

            $this->dispatch('hide-modal');
            $this->dispatch('refreshDatatable');

            // session()->flash('success', 'Beneficiary Activated Successfully!');
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Beneficiary Activated Successfully!'
            ]);

            return redirect()->to('/jnmp-data');
        } catch (\Exception $e) {

            DB::rollBack();
            session()->flash('error', 'Something went wrong!');

            throw $e;
        }
    }
    public function resetForm()
    {
        $this->reset([
            'revert_reason_cause_id',
            'revert_reason_remarks',
        ]);
    }
    public function render()
    {
        return view('livewire.reactivate-modal', [
            'application_id' => $this->applicantId
        ]);
    }
}
