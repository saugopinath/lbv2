<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryEnclosure;
use App\Models\Codemaster;
use App\Models\UpdateBenDetails;
use Illuminate\Support\Facades\DB;

class ReactivateModal extends Component
{
    public $applicantId;

    // beneficiary details
    public $beneficiary_id, $jnmp_name, $dod, $name, $gender, $mobile, $father_name;
    public $dob, $aadhar_no;

    public $reactive_reason;
    public $revert_reason_cause_id, $revert_reason_remarks;

    #[On('showReactivateModal')]
    public function openModal($id)
    {
        $this->applicantId = $id;
        // dd($this->applicantId);
        $this->loadBeneficiaryData();
        $this->dispatch('show-modal');
    }

    public function mount()
    {
        // dd($this->applicantId);
        $this->reactive_reason = Codemaster::where('parent_id', Codemaster::getIdByCode(211))->get();
    }


    public function loadBeneficiaryData()
    {
        $record = BeneficiaryCommonList::with([
            'sourceable',
            'sourceable.relationships',
            'sourceable.contact',
            'sourceable.mapping',
            'sourceable.jnmp',
        ])

            ->whereHas('sourceable', function ($q) {
                $q->where('application_id', $this->applicantId)
                    ->where('jnmp_marked', 1);
            })
            ->whereHas('sourceable.mapping', function ($q) {
                $q->where('payment_suspend', 1);
            })
            ->first();
        //   dd($record->sourceable->jnmp );
        if (!$record) {
            return;
        }

        $b = $record->sourceable;

        // Base personal details
        $this->beneficiary_id = $b->beneficiary_id;
        $this->name = trim($b->full_name);
        $this->mobile = $b->mobile_no;
        $this->dob = $b->dob ? date('d-m-Y', strtotime($b->dob)) : '—';
        $this->aadhar_no = $b->aadhar_no;

        // Father name via relation
        $father = $b->relationships->firstWhere(
            'relation_type_id',
            Codemaster::getIdByCode(131)
        );

        $this->father_name = $father->full_name ?? 'N/A';
        // dd($record->jnmp);
        // JNMP Data
        if ($record->sourceable->jnmp) {
            $this->jnmp_name = $record->sourceable->jnmp->deceasedfullname;
            $this->dod = $record->sourceable->jnmp->dateofdeath;
            $this->gender = $record->sourceable->jnmp->genderdesc;
        }
    }


    public function rules()
    {
        return [
            'revert_reason_cause_id' => 'required',
            'revert_reason_remarks' => 'required|max:100',
        ];
    }


    // public function saveDsMark()
    // {
    //     $this->validate([
    //         'revert_reason_cause_id' => 'required',
    //         'revert_reason_remarks' => 'required|max:100',
    //     ]);

    //     // -----------------------------
    //     // Document must be uploaded
    //     // -----------------------------
    //     $uploadedDocsCount = BeneficiaryEnclosure::where('application_id', $this->applicantId)
    //         ->whereIn('document_type', [169])
    //         ->count();

    //     if ($uploadedDocsCount < 1) {
    //         $this->addError('document_upload', 'Please upload the required document.');
    //         return; // STOP HERE, don't continue
    //     }

    //     DB::beginTransaction();

    //     try {

    //         // 1. Fetch beneficiary
    //         $ben = BeneficiaryCommonList::with([
    //             'sourceable',
    //             'sourceable.relationships',
    //             'sourceable.contact',
    //             'sourceable.mapping',
    //             'sourceable.jnmp',
    //         ])
    //             ->whereHas(
    //                 'sourceable',
    //                 fn($q) =>
    //                 $q->where('application_id', $this->applicantId)
    //             )
    //             ->firstOrFail();

    //         $personal = $ben->sourceable;
    //         $mapping  = $personal->mapping;

    //         // 2. Update values
    //         $mapping->payment_suspend = null;
    //         $mapping->save();

    //         $personal->jnmp_remarks    = $this->revert_reason_remarks;
    //         $personal->reactive_reason = $this->revert_reason_cause_id;
    //         $personal->save();

    //         // 3. Log
    //         UpdateBenDetails::create([
    //             'beneficiary_id' => $personal->beneficiary_id,
    //             'application_id' => $this->applicantId,

    //             'old_data' => json_encode(['payment_suspended' => 1]),
    //             'new_data' => json_encode(['payment_suspended' => null]),

    //             'update_code' => 17,
    //             'remarks' => $this->revert_reason_remarks,
    //             'reactive_reason' => $this->revert_reason_cause_id,
    //             'user_id' => auth()->id(),

    //             'next_level_role_id' => $personal->next_level_role_id,
    //             'dist_code'          => $personal->district_id,

    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         DB::commit();

    //         $this->dispatch('hide-modal');
    //         $this->dispatch('refreshDatatable');
    //         session()->flash('success', 'Beneficiary Activated Successfully!');

    //         return redirect()->to('/jnmp-data');
    //     } catch (\Exception $e) {

    //         DB::rollBack();
    //         session()->flash('error', 'Something went wrong!');
    //         throw $e;
    //     }
    // }


    public function saveDsMark()
    {
        $this->validate([
            'revert_reason_cause_id' => 'required',
            'revert_reason_remarks' => 'required|max:100',
        ]);

        // -----------------------------
        // Document must be uploaded
        // -----------------------------
        $uploadedDocsCount = BeneficiaryEnclosure::where('application_id', $this->applicantId)
            ->whereIn('document_type', [169])
            ->count();

        if ($uploadedDocsCount < 1) {
            $this->addError('document_upload', 'Please upload the required document.');
            return; // STOP HERE, don't continue
        }

        DB::beginTransaction();

        try {

            // 1. Fetch beneficiary
            $ben = BeneficiaryCommonList::with([
                'sourceable',
                'sourceable.relationships',
                'sourceable.contact',
                'sourceable.mapping',
                'sourceable.jnmp',
            ])
                ->whereHas(
                    'sourceable',
                    fn($q) =>
                    $q->where('application_id', $this->applicantId)
                )
                ->firstOrFail();

            $personal = $ben->sourceable;
            $mapping  = $personal->mapping;

            // 2. Update values
            $mapping->payment_suspend = null;
            $mapping->save();

            $personal->jnmp_remarks    = $this->revert_reason_remarks;
            $personal->reactive_reason = $this->revert_reason_cause_id;
            $personal->save();

            // 3. Log
            UpdateBenDetails::create([
                'beneficiary_id' => $personal->beneficiary_id,
                'application_id' => $this->applicantId,

                'old_data' => json_encode(['payment_suspended' => 1]),
                'new_data' => json_encode(['payment_suspended' => null]),

                'update_code' => 17,
                'remarks' => $this->revert_reason_remarks,
                'reactive_reason' => $this->revert_reason_cause_id,
                'user_id' => auth()->id(),

                'next_level_role_id' => $personal->next_level_role_id,
                'dist_code'          => $personal->district_id,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $this->dispatch('hide-modal');
            $this->dispatch('refreshDatatable');
            session()->flash('success', 'Beneficiary Activated Successfully!');

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
        // dd($this->applicantId);
        return view('livewire.reactivate-modal', [
            'application_id' => $this->applicantId
        ]);
    }
}
