<?php

namespace App\Livewire;

use \Carbon\Carbon;
use App\Models\Codemaster;
use App\Models\BenRejectDetail;
use App\Helpers\EncryptionArray;
use App\Models\BenRejectDetails;
use App\Models\BeneficiaryPersonal;
use App\Exports\BeneficiariesExport;
use App\Helpers\CheckAuthHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Crypt;
use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class BeneficiaryTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward, $sub_div;
    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public $relationFather;
    public $schemeName, $schemeId;
    public function mount(string $reportType = '', $schemeName = null, $schemeId = null): void
    {
        $this->schemeId = $schemeId;
        $this->reportType = $reportType;
        $this->relationFather = Codemaster::getIdByCode(131);

        // dd($this->relationFather);
        $select_lgd = session('lgd_session');

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
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
        $this->sub_div = $filters['subdivision_id'];
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
        // if ($this->reportType == '4') {
        //     $columns = [
        //         Column::make("Application ID", "application_id")
        //             ->label(fn($row) => $row->application_id ?? 'N/A'),

        //         Column::make("Applicant Name")
        //             ->label(function ($row) {
        //                 return $row->personal_details[0]['full_name'] ?? 'N/A';
        //             }),

        //         Column::make("Father's Name")
        //             ->label(function ($row) {
        //                 return collect($row->relationship_details)
        //                     ->firstWhere('relation_type_id', $this->relationFather)['full_name'] ?? 'N/A';
        //             }),

        //         Column::make("Age")
        //             ->label(function ($row) {
        //                 $dob = $row->personal_details[0]['dob'] ?? null;
        //                 return $dob ? Carbon::parse($dob)->age : 'N/A';
        //             }),

        //         Column::make("Mobile No.")
        //             ->label(function ($row) {
        //                 return $row->personal_details[0]['mobile_no'] ?? 'N/A';
        //             }),
        //     ];

        //     return $columns;
        // }
        $columns = [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->application_id ?? 'N/A'),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->beneficiary_name ?? 'N/A'),

            Column::make("Father's Name")
                ->label(fn($row) => $row->ben_father_name ?? 'N/A'),

            Column::make("Age", "dob")
                ->label(fn($row) => $row->dob ?? 'N/A'),
            // Column::make("Age", "dob")
            // ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
        ];

        if (in_array($this->reportType, ['1', '5', '4'])) {
            $columns[] = Column::make("Applicant Mobile No.", "mobile_no")
                ->label(fn($row) => $row->mobile_no ?? 'N/A');
        }


        if ($this->reportType == '3') {
            $beneficiaryColumn = Column::make("Beneficiary ID", "beneficiary_id")
                ->label(fn($row) => $row->beneficiary_id ?? 'N/A');

            array_unshift($columns, $beneficiaryColumn);
        }


        // if ($this->reportType == '4') {
        //     $columns[2] = Column::make("Father's Name", "father_full_name");
        //     $columns[] = Column::make("Rejected Reason", "rejected_reason");
        // }



        $columns[] = Column::make("Actions")
            ->label(function ($row) {
                if (($this->reportType == '3') || ($this->reportType == '2')) {
                    return view('coulmn_button.view', [
                        'link' => route('custom_application.view', [
                            'id'        => Crypt::encryptString($row->application_id),
                            'scheme_id' => Crypt::encryptString($row->scheme_id)
                        ]),
                        'tooltip' => 'View Application',
                    ])->render();
                } elseif ((($this->reportType == '1') || ($this->reportType == '6') || ($this->reportType == '5')) && (CheckAuthHelper::isCommonOperator())) {
                    return view('coulmn_button.actions', [
                        'link' => route('draftedit') . '?app_id=' . Crypt::encryptString($row->application_id) . '&ben_id=' . Crypt::encryptString($row->beneficiary_id),
                        'tooltip' => 'Edit Application',
                    ])->render();
                } else {
                    return 'N/A';
                }
            })
            ->html();

        if ($this->reportType === '2') {
            return $columns;
        }

        return $columns;
    }

    // public function builder(): Builder
    // {
    //     $entryVerified  = Codemaster::getIdByCode(23);
    //     $entryApproved  = Codemaster::getIdByCode(0);
    //     $entryPartial = Codemaster::getIdByCode(21);
    //     $entryFinal  = Codemaster::getIdByCode(22);

    //     $next_level_role_id = null;
    //     $sourceableClass = null;

    //     if ($this->reportType == "2") {
    //         $sourceableClass = DraftBeneficiaryPersonal::class;
    //         $next_level_role_id = $entryVerified;
    //     } elseif ($this->reportType == "6") {
    //         $sourceableClass = DraftBeneficiaryPersonal::class;
    //         $next_level_role_id = $entryFinal;
    //     } elseif ($this->reportType == "3") {
    //         $sourceableClass = BeneficiaryPersonal::class;
    //         $next_level_role_id = $entryApproved;
    //     } elseif ($this->reportType == "1") {
    //         $sourceableClass = DraftBeneficiaryPersonal::class;
    //         $next_level_role_id = $entryPartial;
    //     } elseif ($this->reportType == "5") {
    //         $sourceableClass = DraftBeneficiaryPersonal::class;
    //         $extraConditions = ['is_final_submit' => true];
    //          $next_level_role_id = $entryPartial;
    //         $query = BeneficiaryCommonList::whereHasMorph('sourceable', $sourceableClass, function ($q) use ($next_level_role_id, $extraConditions) {
    //             if (!empty($extraConditions)) {
    //                 foreach ($extraConditions as $field => $value) {
    //                     $q->where($field, $value);
    //                 }
    //             }
    //             $q->where('next_level_role_id', $next_level_role_id);
    //         });
    //     } elseif ($this->reportType == "4") {
    //         $query = BenRejectDetails::query();
    //         // dd($query->get());

    //         return EncryptionArray::applyLocationFilter(
    //             $query,
    //             $this->reportType,
    //             $this->district_id ? (int) $this->district_id : null,
    //             $this->rural_urban ? (int) $this->rural_urban : null,
    //             $this->blockurban ? (int) $this->blockurban : null,
    //             $this->gp_ward ? (int) $this->gp_ward : null,
    //             $this->sub_div ? (int) $this->sub_div : null
    //         );
    //     }
    //     $query = BeneficiaryCommonList::with('sourceable.contact', 'sourceable.relationships')
    //         ->whereHasMorph(
    //             'sourceable',
    //             $sourceableClass,
    //             function ($q) use ($next_level_role_id) {
    //                 $q->where('next_level_role_id', $next_level_role_id);
    //             }
    //         );

    //     // dd($query->get());
    //     if (!empty($this->filter_condition)) {
    //         $query->where($this->filter_condition);
    //     }


    //     if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
    //         // dd($this->gp_ward);
    //         $query = EncryptionArray::applyLocationFilter(
    //             $query,
    //             $this->reportType,
    //             $this->district_id ? (int) $this->district_id : null,
    //             $this->rural_urban ? (int) $this->rural_urban : null,
    //             $this->blockurban ? (int) $this->blockurban : null,
    //             $this->gp_ward ? (int) $this->gp_ward : null,
    //             $this->sub_div ? (int) $this->sub_div : null
    //         );
    //     }

    //     $this->dispatch('hideLoader');
    //     return $query;
    // }

    public function builder(): Builder
    {
        // Status Constants
        $STATUS_VERIFIED = 1;
        $STATUS_APPROVED = 2;
        $STATUS_FINAL    = 0;
        $STATUS_REJECT   = -100;
        $STATUS_REVERT   = -20;
        $STATUS_PARTIAL  = null;

        $nextLevelRoleId = null;

        // Default condition
        $extraConditions = [
            'scheme_id' => $this->schemeId
        ];

        switch ($this->reportType) {

            case "1": // Partial
                $extraConditions['is_final'] = 0;
                $nextLevelRoleId = $STATUS_PARTIAL;
                break;

            case "2": // Verified
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_VERIFIED;
                break;

            case "3": // Approved
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_APPROVED;
                break;

            case "4": // Rejected
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_REJECT;
                break;

            case "5": // Reverted
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_REVERT;
                break;

            case "6": // Final
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_FINAL;
                break;
        }

        /**
         * --- BASE QUERY ---
         */
        $query = BeneficiaryPersonalDetail::with('contact')
            ->where(function ($q) use ($extraConditions, $nextLevelRoleId) {

                foreach ($extraConditions as $field => $value) {
                    $q->where($field, $value);
                }

                $q->where('next_level_role_id', $nextLevelRoleId);
            });

        /**
         * --- EXTRA FILTER CONDITION ---
         */
        if (!empty($this->filter_condition)) {
            $query->where($this->filter_condition);
        }

        /**
         * --- LOCATION FILTER ---
         */
        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->district_id ?: null,
                $this->rural_urban ?: null,
                $this->blockurban ?: null,
                $this->gp_ward ?: null,
                $this->sub_div ?: null
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
    public function exportExcel()
    {
        $data = $this->builder()->get()->map(function ($row) {
            return [
                'application_id' => $row->sourceable->application_id ?? 'N/A',
                'full_name' => $row->sourceable->full_name ?? 'N/A',
                'father_name' => optional($row->sourceable->relationships
                    ->where('relation_type_id', $this->relationFather ?? null)
                    ->first())->full_name ?? 'N/A',
                'dob' => $row->sourceable->dob ?? 'N/A',
                'mobile_no' => $row->sourceable->mobile_no ?? 'N/A',
            ];
        });

        return Excel::download(new BeneficiariesExport($data), 'beneficiaries_all.xlsx');
    }
}
