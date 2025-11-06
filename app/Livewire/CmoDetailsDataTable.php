<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
use App\Helpers\EncryptionArray;
use App\Exports\BeneficiariesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use App\Models\Codemaster;
use Carbon\Carbon;
use App\Models\AcceptRejectInfo;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CmoSmData;

class CmoDetailsDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward, $next_level_role_id, $revertrejectAction, $revertrejectCauses, $sub_div;
    // protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public $process_type, $initialMobile;

    protected $listeners = ['processTypeChanged' => 'updateProcessType'];

    public function updateProcessType($type)
    {
        $this->process_type = $type;
    }

    #[On('searchTriggered')]
    public function handleSearchTriggered($selectedOption, $inputValue)
    {
        dd($selectedOption, $inputValue);
    }

    public function mount($initialMobile): void
    {
        // dd($initialMobile);
        $this->initialMobile = $initialMobile;
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
            // ->setBulkActionsEnabled()
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
                ->label(fn($row) => $row->sourceable->application_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('sourceable', function ($q) use ($searchTerm) {
                        $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->sourceable->full_name ?? 'N/A'),

            Column::make("Father's Name", "fullname")
                ->label(function ($row) {
                    return optional(
                        $row->sourceable->relationships->firstWhere(
                            'relation_type_id',
                            Codemaster::getIdByCode(131)
                        )
                    )->full_name ?? 'N/A';
                }),

            Column::make("Mobile No", "Mobile No")
                ->label(fn($row) => $row->sourceable->mobile_no
                    ?? 'N/A'),

            Column::make("Address", "Address")
                ->label(fn($row) => $row->sourceable->contact->getFullAddress() ?? 'N/A')
                ->html(),

            Column::make("Status", "Status")
                ->label(fn($row) => $row->sourceable->getStatusText()
                    ?? 'N/A'),

            $columns[] = Column::make("Actions")
                ->label(function ($row) {
                    return view('coulmn_button.view', [
                        'link' => route('draft-application.view', Crypt::encryptString($row->sourceable->application_id)),
                        'tooltip' => 'Process',
                    ])->render();
                })
                ->html(),
        ];
    }
    public function builder(): Builder
    {
        $query = BeneficiaryCommonList::with('sourceable.relationships', 'sourceable.contact');
        if ($this->initialMobile) {
            $query->where('mobile_no', $this->initialMobile);
        }
        // if (!empty($this->filter_condition)) {
        //     foreach ($this->filter_condition as $column => $value) {
        //         if (!empty($value)) {
        //             $query->where($column, $value);
        //         }
        //     }
        // }
        return $query;
    }


    public function exportExcel()
    {
        $data = $this->builder()->get()->map(function ($row) {
            return [
                'Application ID' => $row->sourceable->application_id ?? 'N/A',
                'Applicant Name' => $row->sourceable->full_name ?? 'N/A',
                'Father Name' => optional(
                    $row->sourceable->relationships->firstWhere(
                        'relation_type_id',
                        Codemaster::getIdByCode(131)
                    )
                )->full_name ?? 'N/A',
                'DOB' => $row->sourceable->dob ?? 'N/A',
                'Mobile' => $row->sourceable->mobile_no ?? 'N/A',
            ];
        });

        return Excel::download(new BeneficiariesExport($data), 'applications_all.xlsx');
    }
}
