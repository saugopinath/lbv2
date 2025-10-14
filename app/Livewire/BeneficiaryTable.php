<?php

namespace App\Livewire;

use \Carbon\Carbon;
use App\Models\Codemaster;
use App\Models\BenRejectDetail;
use App\Helpers\EncryptionArray;
use App\Models\BeneficiaryPersonal;
use App\Exports\BeneficiariesExport;
use App\Models\BeneficiaryCommonList;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class BeneficiaryTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward;
    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public $relationFather;
    public function mount(string $reportType = ''): void
    {
        $this->reportType = $reportType;
        $this->relationFather = Codemaster::getIdByCode(131);

        // dd($this->relationFather);
        $select_lgd = session('lgd_session');

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
        // dd($filters['gp_ward']);
        $this->district_id = $filters['district_id'];
        // dd($this->district_id );
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
        // dd($this->gp_ward );
    }
    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchdisabled()
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();

        $this->setConfigurableAreas([
            'toolbar-left-start' => 'livewire.export_excel_buttons',
        ]);

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
    public function columns(): array
    {
        $columns = [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->sourceable->application_id ?? 'N/A'),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->sourceable->full_name ?? 'N/A'),
            Column::make("Father's Name")
                ->label(fn($row) => $row->sourceable->relationships
                    ->where('relation_type_id', $this->relationFather)->first()?->full_name),
            Column::make('Age')
                ->label(function ($row) {
                    $dob = $row->sourceable->dob ?? null;
                    return $dob ? Carbon::parse($dob)->age : 'N/A';
                }),
            // Column::make("Age", "dob")
            // ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
        ];

        if (in_array($this->reportType, ['1', '5', '4'])) {
            $columns[] = Column::make("Applicant Mobile No.", "mobile_no");
        }

        if ($this->reportType === '3') {
            $beneficiaryColumn = Column::make("Beneficiary ID", "beneficiary_id")
                ->label(fn($row) => $row->sourceable->beneficiary_id ?? 'N/A');

            array_unshift($columns, $beneficiaryColumn);
        }


        if ($this->reportType === '4') {
            $columns[2] = Column::make("Father's Name", "father_full_name");
            $columns[] = Column::make("Rejected Reason", "rejected_reason");
        }



        $columns[] = Column::make("Actions")
            ->label(function ($row) {
                if ($this->reportType != '4') {
                    return view('coulmn_button.view', [
                        'link' => route('custom_application.view', [
                            // 'application_id' => Crypt::encrypt($row->application_id),
                            'id' => $this->reportType == '3' ? encrypt($row->sourceable->application_id) : encrypt($row->sourceable->application_id),
                            'reportType' => $this->reportType,
                        ]),
                        'tooltip' => 'View Application',
                    ])->render();
                }
            })
            ->html();

        if ($this->reportType === '2') {
            return $columns;
        }

        return $columns;
    }

    public function builder(): Builder
    {
        $roleVerified  = Codemaster::getIdByCode(23);
        $roleApproved  = Codemaster::getIdByCode(0);
        $roleReverted  = Codemaster::getIdByCode(21);

        $next_level_role_id = null;
        $sourceableClass = null;

        // 🔹 Decide which model type to use based on reportType
        if ($this->reportType === "2") {
            $sourceableClass = DraftBeneficiaryPersonal::class;
            $next_level_role_id = $roleVerified;
        } elseif ($this->reportType === "3") {
            $sourceableClass = BeneficiaryPersonal::class;
            $next_level_role_id = $roleApproved;
        } elseif (in_array($this->reportType, ["1", "5"])) {
            $sourceableClass = DraftBeneficiaryPersonal::class;
            $next_level_role_id = $roleReverted;
        } elseif ($this->reportType === "4") {
            $query = BenRejectDetail::query();

            return EncryptionArray::applyLocationFilter(
                $query,
                $this->reportType,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null
            );
        }

        $query = BeneficiaryCommonList::with('sourceable.contact', 'sourceable.relationships')
            ->whereHasMorph(
                'sourceable',
                $sourceableClass,
                function ($q) use ($next_level_role_id) {
                    $q->where('next_level_role_id', $next_level_role_id);
                }
            );

        if (!empty($this->filter_condition['district_id'])) {
            $districtId = $this->filter_condition['district_id'];

            $query->whereHasMorph(
                'sourceable',
                $sourceableClass,
                function ($q) use ($districtId) {
                    $q->whereHas('contact', function ($contactQuery) use ($districtId) {
                        $contactQuery->where('district_id', $districtId);
                    });
                }
            );
        }

        if (!empty($this->filter_condition['block_id'])) {
            $blockId = $this->filter_condition['block_id'];

            $query->whereHasMorph(
                'sourceable',
                $sourceableClass,
                function ($q) use ($blockId) {
                    $q->whereHas('contact', function ($contactQuery) use ($blockId) {
                        $contactQuery->where('block_id', $blockId);
                    });
                }
            );
        }

        if (!empty($this->filter_condition['subdivision_id'])) {
            $subdivisionId = $this->filter_condition['subdivision_id'];

            $query->whereHasMorph(
                'sourceable',
                $sourceableClass,
                function ($q) use ($subdivisionId) {
                    $q->whereHas('contact.municipality', function ($municipalityQuery) use ($subdivisionId) {
                        $municipalityQuery->where('subdivision_id', $subdivisionId);
                    });
                }
            );
        }

        // dd($this->gp_ward);
        // $query = EncryptionArray::applyLocationFilter(
        //     $query,
        //     $this->reportType,
        //     $this->district_id ? (int) $this->district_id : null,
        //     $this->rural_urban ? (int) $this->rural_urban : null,
        //     $this->blockurban ? (int) $this->blockurban : null,
        //     $this->gp_ward ? (int) $this->gp_ward : null
        // );


        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            // dd($this->gp_ward);
            $query = EncryptionArray::applyLocationFilter(
                $query,
                $this->reportType,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null
            );
        }

        $this->dispatch('hideLoader');
        return $query;
    }


    public function filters(): array
    {
        return [
            TextFilter::make('Application ID')
                ->filter(function ($query, $value) {
                    $query->whereHas('sourceable', function ($q) use ($value) {
                        $q->where('application_id', 'ILIKE', "%{$value}%");
                    });
                }),

            TextFilter::make('Beneficiary ID')
                ->filter(function ($query, $value) {
                    $query->whereHas('sourceable', function ($q) use ($value) {
                        $q->where('beneficiary_id', 'ILIKE', "%{$value}%");
                    });
                }),

            TextFilter::make('Applicant Name')
                ->filter(function ($query, $value) {
                    $query->whereHas('sourceable', function ($q) use ($value) {
                        $q->where('full_name', 'ILIKE', "%{$value}%");
                    });
                }),
        ];
    }
    // public function export()
    // {
    //     $reportTypeFormatted = ucfirst($this->reportType);
    //     $timestamp = Carbon::now('Asia/Kolkata')->format('Ymd_Hi');
    //     $filename = "{$reportTypeFormatted}_Beneficiaries_{$timestamp}.xlsx";
    //     $select_lgd = session('lgd_session');
    //     $login_type =  Crypt::decryptString($select_lgd['office_type_id']);


    //     return Excel::download(
    //         new BeneficiariesExport(
    //             $this->reportType,
    //             $login_type,
    //             $this->loginDistrictCode,
    //             $this->loginSubdivisionCode,
    //             $this->loginBlockCode
    //         ),
    //         $filename
    //     );
    // }
    // public function render(): \Illuminate\View\View
    // {
    //     $this->dispatch('hideLoader');
    //     return view('livewire.custom-beneficiary-table', [
    //         'rows' => $this->getRows(),
    //         'reportType' => $this->reportType,
    //     ]);
    // }
}
