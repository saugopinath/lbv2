<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
use App\Exports\BeneficiariesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use App\Models\Codemaster;
use App\Models\BackFromJb;

class BackFromJBDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    protected $listeners = [
        'doSearch' => 'updateFilters',
    ];
    public array $filter_condition = [];
    public function mount(): void
    {
        $select_lgd = session('lgd_session');
        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['lb_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['lb_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['lb_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }

    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setColumnSelectDisabled()
        ;
        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white',
        ]);
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-4 py-3 text-white bg-violet-800 text-xs',
            ];
        });

        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-4 py-3 text-gray-700 text-center',
            ];
        });

        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
    }

    public function updateFilters($filters)
    {
        $this->resetPage();
    }
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
    public function filters(): array
    {
        return [];
    }

    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->beneficiary->sourceable->application_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('sourceable', function ($q) use ($searchTerm) {
                        $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->beneficiary->sourceable->full_name ?? 'N/A'),

            Column::make("Mobile No", "Mobile No")
                ->label(fn($row) => $row->beneficiary->sourceable->mobile_no
                    ?? 'N/A'),

            Column::make("Address", "Address")
                ->label(fn($row) => $row->beneficiary->sourceable->contact->getFullAddress() ?? 'N/A')
                ->html(),

            Column::make("Status", "Status")
                ->label(fn($row) => $row->beneficiary->sourceable->getStatusText()
                    ?? 'N/A'),


        ];
    }

    public function builder(): Builder
    {
        // $val = BackFromJb::with('beneficiary.sourceable')->get();
        // dd($val);
        return BackFromJb::query()
            ->with([
                'beneficiary.sourceable.contact'
            ]);
    }
}
