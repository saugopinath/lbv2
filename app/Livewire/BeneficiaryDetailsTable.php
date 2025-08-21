<?php

namespace App\Livewire;

use App\Models\BeneficiaryApprovedList;
use \Carbon\Carbon;
use App\Models\Codemaster;
use App\Models\BenRejectDetail;
use App\Helpers\EncryptionArray;
use App\Models\BeneficiaryPersonal;
use App\Exports\BeneficiariesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class BeneficiaryDetailsTable extends DataTableComponent
{
    public ?int $perPage = 1;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward;
    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public function mount(): void
    {
        $select_lgd = session('lgd_session');

        // foreach ($select_lgd as $key => $val) {
        //     $this->filter_condition[$key] = Crypt::decryptString($val);
        // }

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }
    public function filtersApplied($filters)
    {
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
    }
    public function configure(): void
    {   

        $this->setPrimaryKey('application_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([1, 5])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setBulkActionsEnabled()
            ->setSelectAllEnabled();
        //      $this->setTableAttributes([
        // 'class' => 'min-w-full divide-y divide-gray-200 bg-white shadow-md rounded-lg p-4',
        // ]);
         
        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto border rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 text-white',
        ]);
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-4 py-3 text-white/70 bg-violet-800 text-xs',
            ];
        });

        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-4 py-2 text-gray-700 text-center',
            ];
        });

        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
    }
    // public function setConfigurableArea():
    // {
    //     $this->configurableAreas = $areas;

    //     return $this;
    // }

    public function updatedSearch($value): void
    {
        $this->setSearch($value);
        $this->resetPage();
    }
    public function updatedPerPage($value): void
    {
        $this->perPage = (int)$value;
        $this->setPerPage((int)$value);
        $this->resetPage();
    }
    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->sourceable->application_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('sourceable', function ($q) use ($searchTerm) {
                        $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            // ->label(fn($row) => $row->sourceable->application_id ?? 'N/A'),

            Column::make("Beneficiary ID", "beneficiary_id")
                ->label(fn($row) => $row->sourceable->beneficiary_id ?? 'N/A'),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->sourceable->full_name ?? 'N/A'),

            Column::make("Mobile No", "mobile_no")
                ->label(fn($row) => $row->sourceable->mobile_no ?? 'N/A'),

            Column::make("Bank AC No", "bank_account_number")
                ->label(fn($row) => $row->sourceable->bank->bank_account_number ?? 'N/A'),

            Column::make("IFSC", "ifsc")
                ->label(fn($row) => $row->sourceable->bank->ifsc ?? 'N/A'),

            Column::make("Branch", "branch")
                ->label(fn($row) => $row->sourceable->bank->ifscMaster->branch ?? 'N/A'),

            Column::make("Bank Name", "bank_name")
                ->label(fn($row) => $row->sourceable->bank->ifscMaster->bankmaster->name ?? 'N/A'),

            Column::make("Type")
                ->label(fn($row) => class_basename($row->sourceable_type)),
        ];
    }
    public function builder(): Builder
    {
        // $query = BeneficiaryApprovedList::with('sourceable.bank')
        //     ->with(['contact' => fn($q) => $q->where('relation_type_id', $relationFather)]);

        // return $query;
        $query = BeneficiaryApprovedList::with('sourceable.contact', 'sourceable.bank');

        if (!empty($this->filter_condition['district_id'])) {
            $query->whereHas(
                'sourceable.contact',
                fn($q) =>
                $q->where('district_id', $this->filter_condition['district_id'])
            );
        }

        if (!empty($this->filter_condition['block_id'])) {
            $query->whereHas(
                'sourceable.contact',
                fn($q) =>
                $q->where('block_id', $this->filter_condition['block_id'])
            );
        }

        if (!empty($this->filter_condition['subdivision_id'])) {
            $query->whereHas(
                'sourceable.contact.municipality',
                fn($mq) =>
                $mq->where('subdivision_id', $this->filter_condition['subdivision_id'])
            );
        }
        // $result = $query->get();
        // dd($result); 
        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null
            );
        }
        return $query;
    }
    // public function export()
    // {
    //     $reportTypeFormatted = ucfirst($this->reportType);
    //     $timestamp = Carbon::now('Asia/Kolkata')->format('Ymd_Hi');
    //     $filename = "{$reportTypeFormatted}_Beneficiaries_{$timestamp}.xlsx";

    //     return Excel::download(
    //         new BeneficiariesExport(
    //             $this->reportType,
    //             $this->login_type,
    //             $this->loginDistrictCode,
    //             $this->loginSubdivisionCode,
    //             $this->loginBlockCode
    //         ),
    //         $filename
    //     );
    // }?
    // public function render(): \Illuminate\View\View
    // {
    //     return view('livewire.custom-beneficiary-table', [
    //         'rows' => $this->getRows(),
    //     ]);
    // }
}
