<?php

namespace App\Livewire\DynamicWorkflow;

use App\Models\Scheme;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\DynamicWorkflowModule;
use App\Models\workflowstepRolemapping;
use App\Models\DynamicWorkflowRequest;
use App\Services\DynamicWorkflowService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RequestUpdateBeneficiary extends Component
{
    public $searchId;
    public $selectedScheme;
    public $beneficiary = null;

    // Workflow related
    public $selectedModule;
    public $availableModules = [];

    // Field Selection
    public $showFields = false;
    public $selectedFields = [];
    public $fieldOptions = [
        'mobile_no' => 'Mobile Number',
        'bank_account_number' => 'Bank Account Number',
        'bank_ifsc' => 'Bank IFSC',
        'caste' => 'Caste Category',
        'ben_name' => 'Beneficiary Name'
    ];
    public $moduleId = 12;
    public $schemeId = 20;

    // Data modification
    public $oldData = [];
    public $newData = [];
    public $items = [];
    public $filter_condition = [];

    protected $listeners = [
        'beneficiary-search' => 'handleSearch'
    ];

    public function mount()
    {
        $select_lgd = session('lgd_session');
        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = \Illuminate\Support\Facades\Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = \Illuminate\Support\Facades\Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = \Illuminate\Support\Facades\Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }

    public function handleSearch($data)
    {
        // dd($data);
        if (empty($data['results'])) {
            // dd('njnbj');
            $this->items = [];
            $this->dispatch('toast', 'error', 'No matching approved beneficiary found.');
            return;
        }

        $applicationIds = collect($data['results'])->pluck('application_id')->toArray();

        $this->items = BeneficiaryPersonalDetail::query()
            ->select(['application_id', 'beneficiary_id', 'scheme_id', 'beneficiary_name', 'other_details'])
            ->with([
                'contact:beneficiary_id,application_id,scheme_id,district_id,rural_urban,blockurban,gpward',
                'bank:beneficiary_id,application_id,scheme_id,bankaccountnumber,ifscode'
            ])
            ->whereIn('application_id', $applicationIds)
            // ->when(!empty($this->filter_condition), function ($query) {
            //     foreach ($this->filter_condition as $key => $value) {
            //         $query->where($key, $value);
            //     }
            // })
            ->get()
            ->map(fn($item) => [
                'application_id' => $item->application_id,
                'beneficiary_id' => $item->beneficiary_id,
                'applicant_name' => $item->beneficiary_name,
                'mobile_no'      => $item->other_details['mobile_no'] ?? '-',
                'address'        => optional($item->contact)->getFullAddress() ?? 'N/A',
                'bank_account'   => optional($item->bank)->bankaccountnumber ?? '-',
                'ifsc'           => optional($item->bank)->ifscode ?? '-',
                'scheme_id'      => $item->scheme_id,
            ])->toArray();
        // dd($this->items);
    }

    // public function selectBeneficiary($appId, $schemeId)
    // {
    //     $this->selectedScheme = $schemeId;
    //     $this->beneficiary = BeneficiaryPersonalDetail::where('application_id', $appId)->first();
    //     $this->availableModules = DynamicWorkflowModule::where('scheme_id', $this->selectedScheme)->get();
    //     $this->showFields = false;
    //     $this->selectedFields = [];
    //     $this->items = []; // টেবিলটি হাইড করে শুধু আপডেট ফর্মটি রাখার জন্য
    // }

    // public function updatedSelectedScheme()
    // {
    //     $this->availableModules = DynamicWorkflowModule::where('scheme_id', $this->selectedScheme)->get();
    //     $this->beneficiary = null;
    // }

    // public function toggleUpdate()
    // {
    //     $this->showFields = true;
    //     // Reset old data from beneficiary object
    //     foreach ($this->fieldOptions as $key => $label) {
    //         $this->oldData[$key] = $this->beneficiary->$key ?? '';
    //         $this->newData[$key] = $this->beneficiary->$key ?? '';
    //     }
    // }

    // public function submitRequest()
    // {
    //     if (empty($this->selectedFields)) {
    //         $this->dispatch('toast', 'error', 'Please select at least one field to update!');
    //         return;
    //     }

    //     // Get the first step of the module from mapping table
    //     $firstStep = workflowstepRolemapping::where('module_id', $this->selectedModule)
    //         ->orderBy('rank', 'asc')
    //         ->first();

    //     if (!$firstStep) {
    //         $this->dispatch('toast', 'error', 'Workflow steps not configured for this module!');
    //         return;
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $service = new \App\Services\DynamicWorkflowService();
    //         $service->initiateRequest(
    //             $this->selectedModule,
    //             $this->beneficiary->id,
    //             array_intersect_key($this->oldData, array_flip($this->selectedFields)),
    //             array_intersect_key($this->newData, array_flip($this->selectedFields))
    //         );

    //         DB::commit();
    //         $this->dispatch('toast', 'success', 'Update Request Initiated Successfully!');
    //         $this->reset(['beneficiary', 'searchId', 'showFields', 'selectedFields']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         $this->dispatch('toast', 'error', $e->getMessage());
    //     }
    // }


    public function selectBeneficiary($appId)
    {
        $this->beneficiary = BeneficiaryPersonalDetail::with(['bank', 'contact'])
            ->where('application_id', $appId)
            ->first();

        if (!$this->beneficiary) {
            $this->dispatch('toast', 'error', 'Beneficiary not found');
            return;
        }

        $this->showFields = true;

        foreach ($this->fieldOptions as $key => $label) {
            $this->oldData[$key] = match ($key) {
                'mobile_no' => $this->beneficiary->other_details['mobile_no'] ?? '',
                'bank_account_number' => optional($this->beneficiary->bank)->bankaccountnumber ?? '',
                'bank_ifsc' => optional($this->beneficiary->bank)->ifscode ?? '',
                'ben_name' => $this->beneficiary->beneficiary_name ?? '',
                default => ''
            };

            $this->newData[$key] = $this->oldData[$key];
        }

        // Hide search table
        $this->items = [];
    }

    /**
     * Submit workflow request
     */
    public function submitRequest()
    {
        if (!$this->beneficiary) {
            $this->dispatch('toast', 'error', 'No beneficiary selected!');
            return;
        }

        if (empty($this->selectedFields)) {
            $this->dispatch('toast', 'error', 'Select at least one field!');
            return;
        }

        DB::beginTransaction();

        try {
            $service = new DynamicWorkflowService();
            $service->initiateRequest(
                $this->moduleId,
                $this->beneficiary->application_id,
                array_intersect_key($this->oldData, array_flip($this->selectedFields)),
                array_intersect_key($this->newData, array_flip($this->selectedFields))
            );
            DB::commit();
            $this->dispatch('toast', 'success', 'Request submitted successfully!');
            $this->reset([
                'beneficiary',
                'showFields',
                'selectedFields',
                'oldData',
                'newData'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage()); // এরর পাওয়া গেছে, তাই এটি এখন দরকার নেই
            $this->dispatch('toast', 'error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dynamic-workflow.request-update-beneficiary', [
            'schemes' => Scheme::where('is_active', true)->get()
        ]);
    }
}
