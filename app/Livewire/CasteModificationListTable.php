<?php

namespace App\Livewire;

use App\Models\BeneficiaryPersonal;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\BeneficiariesExport;
use App\Helpers\CheckAuthHelper;
use App\Models\Scheme;
use Illuminate\Support\Facades\Crypt;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class CasteModificationListTable extends DataTableComponent
{
    protected $listeners = [
        'refreshDatatable' => '$refresh',
        'filtersApplied'   => 'setFilters',
        'resetFilters'     => 'resetFilters',
        'resettable'  => 'resettable',
    ];
    public int $rowNumberOffset = 0;
    // public int $roleId = 0;
    // public array $filter_condition = [];
    // public string $applicantStatus = '';
    // public string $casteId = '';
    // public bool $showTable = false;   // ✅ control table visibility
    // public bool $showActions = false;
    // public int $action_visible = 0;
    // public int $nextLevelRequestId = 0;
    public string $applicantStatus = '';
    public string $casteId = '';
    public  $schemeId = null;
    public int $roleId = 0;
    public bool $showTable = false;
    public int $action_visible = 0;
    public int $nextLevelRequestId = 0;
    public array $filter_condition = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->rowNumberOffset = ($this->getPage() - 1) * $this->getPerPage();
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
        $this->setLoadingPlaceholderEnabled();
        $this->setConfigurableAreas([
            'toolbar-left-start' => 'livewire.export_excel_buttons',
        ]);
        $this->setSearchDisabled(); // Disable the global search box
    }
    public function mount($applicantStatus = '', $casteId = '', $schemeId = '')
    {
        if (request()->query('retain_filters') == 1) {
            $filters = session('caste_mod_filters', []);
            $this->applicantStatus = $applicantStatus ?: ($filters['status'] ?? '');
            $this->casteId = $casteId ?: ($filters['caste'] ?? '');
            $this->schemeId = $schemeId ?: ($filters['scheme'] ?? '');
        } else {
            $this->applicantStatus = $applicantStatus;
            $this->casteId = $casteId;
            $this->schemeId = $schemeId;
        }

        if (!empty($this->applicantStatus)) {
            $this->showTable = true;
            $this->action_visible = ($this->applicantStatus == 'PL') ? 1 : 0;
        }

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
        if (!empty($select_lgd['role_id'])) {
            $this->roleId = (int) Crypt::decryptString($select_lgd['role_id']);
        }

        // Show actions based on status and role
        // $this->showActions();
    }
    private function getStatusMessage($status): string
    {
        return match ($status) {
            'APL' => 'Verified but Pending for Approval',
            'VPL' => 'Pending for Verification',
            'AL'  => 'Application Already Approved',
            'RL'  => 'Application Already Reverted',
            'VL'  => 'Application Already Verified',
            default => 'No Action Required',
        };
    }

    // protected function showActions(): void
    // {
    //     $this->action_visible = ($this->applicantStatus === 'PL' && auth()->user()?->hasAnyRole(['Verifier', 'Approver'])) ? 1 : 0;
    // }

    // public function mount($applicantStatus = ''): void
    // {
    //     $this->applicantStatus = $applicantStatus;
    //     $select_lgd = session('lgd_session');

    //     if (!empty($select_lgd['district_id'])) {
    //         $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
    //     }

    //     if (!empty($select_lgd['block_id'])) {
    //         $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
    //     }

    //     if (!empty($select_lgd['subdivision_id'])) {
    //         $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
    //     }

    //     if (!empty($select_lgd['role_id'])) {
    //         $this->roleId = (int) Crypt::decryptString($select_lgd['role_id']);
    //     }
    // }
    public function setFilters($filters): void
    {
        // dd('bhbhbjhb');
        $this->applicantStatus = $filters['status'] ?? '';
        $this->casteId         = $filters['caste'] ?? '';
        $this->schemeId        = $filters['scheme'] ?? '';
        $this->action_visible = ($this->applicantStatus == 'PL') ? 1 : 0;
        if (!empty($this->applicantStatus)) {
            $this->showTable = true;
        } else {
            $this->showTable = false;
        }
        // dump($this->applicantStatus);
        // dump($this->casteId);
        // dd($this->action_visible);
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->applicantStatus   = '';
        $this->casteId           = '';
        $this->nextLevelRequestId = 0;
        $this->showTable = false;
        $this->resetPage();
    }
    public function resettable(): void
    {
        $this->showTable = false;
        $this->resetPage();
    }

    // select all value through orm 

    public function builder(): Builder
    {
        if (!$this->showTable) {
            return CasteModificationInfo::query()->whereRaw('1 = 0');
        }
        $query = CasteModificationInfo::query()
            ->select([
                'id',
                'application_id',
                'beneficiary_id',
                'caste_request_type',
                'next_level_requested_id',
                'created_at',
            ])
            ->with([
                'beneficiaryPersonal:application_id,beneficiary_name,ben_father_name,scheme_id'
            ])
            ->whereHas('beneficiaryPersonal', function ($q) {
                $q->where($this->filter_condition);
            })
            ->where('scheme_id', $this->schemeId)
            ->when(
                $this->casteId,
                fn($q) =>
                $q->where('caste_request_type', $this->casteId)
            );

        if (!empty($this->applicantStatus)) {
            if ($this->applicantStatus == 'PL') {
                if (in_array($this->roleId, [4, 5])) {
                    $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
                    $this->action_visible = 1;
                } elseif (in_array($this->roleId, [6, 7])) {
                    $this->nextLevelRequestId = Codemaster::getIdByCode(2201);
                    $this->action_visible = 1;
                }
            } elseif ($this->applicantStatus == 'APL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
            } elseif ($this->applicantStatus == 'VPL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2201);
            } elseif ($this->applicantStatus == 'VL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
            } elseif ($this->applicantStatus == 'AL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2203);
            } elseif ($this->applicantStatus == 'RL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2204);
            } else {
                $this->nextLevelRequestId = 0;
            }
            $query->where('next_level_requested_id', $this->nextLevelRequestId);
        }
        // $query1 = $query;
        // dd($query1);
        // dd($query->get());
        // dump(['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        return $query;
    }


    // select particular column value using orm 

    // public function builder(): Builder
    // {
    //     // minimal select on main model (keep primary key 'id')
    //     $query = CasteModificationInfo::query()
    //         ->select([
    //             'id',
    //             'application_id',
    //             'beneficiary_id',
    //             'caste_request_type',
    //             'next_level_requested_id',
    //         ])
    //         ->with([
    //             // ensure primary key(s) used for relation matching are selected
    //             'beneficiaryCommonList' => function ($q) {
    //                 $q->select([
    //                     // primary key of beneficiary_common_lists is sourceable_id
    //                     'sourceable_id',
    //                     'sourceable_type',
    //                     // include columns you filter by or display (example: district_id)
    //                     // 'district_id',
    //                 ]);
    //             },

    //             // the polymorphic target (BeneficiaryPersonal) must include its PK (application_id)
    //             'beneficiaryCommonList.sourceable' => function ($q) {
    //                 $q->select([
    //                     'application_id', // PK (important!)
    //                     'full_name',
    //                     'beneficiary_id',
    //                     'mobile_no',
    //                 ]);
    //             },

    //             // constrain relationships to father (relation_type_id = 79) and ensure application_id present
    //             'beneficiaryCommonList.sourceable.relationships' => function ($q) {
    //                 $q->select([
    //                     'id',
    //                     'application_id',
    //                     'full_name',
    //                     'relation_type_id',
    //                 ])->where('relation_type_id', 79);
    //             },

    //             'beneficiaryCommonList.sourceable.contact' => function ($q) {
    //                 $q->select([
    //                     'id',
    //                     'application_id',

    //                 ]);
    //             },
    //         ]);

    //     // Only apply whereHas if filter_condition is present and is non-empty array
    //     if (!empty($this->filter_condition) && is_array($this->filter_condition)) {
    //         $query->whereHas('beneficiaryCommonList', function ($q) {
    //             foreach ($this->filter_condition as $col => $val) {
    //                 // Use exact column names from beneficiary_common_lists here
    //                 $q->where($col, $val);
    //             }
    //              $q->where('sourceable_type', BeneficiaryPersonal::class);
    //         });
    //     }

    //     // caste filter
    //     if (!empty($this->casteId)) {
    //         $query->where('caste_request_type', $this->casteId);
    //     }

    //     // applicantStatus -> compute nextLevelRequestId (only add where if > 0)
    //     if (!empty($this->applicantStatus)) {
    //         // existing logic to set $this->nextLevelRequestId and $this->action_visible
    //         if ($this->applicantStatus == 'PL') {
    //             if (in_array($this->roleId, [4, 5])) {
    //                 $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
    //                 $this->action_visible = 1;
    //             } elseif (in_array($this->roleId, [6, 7])) {
    //                 $this->nextLevelRequestId = Codemaster::getIdByCode(2201);
    //                 $this->action_visible = 1;
    //             }
    //         } elseif ($this->applicantStatus == 'APL') {
    //             $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
    //         } elseif ($this->applicantStatus == 'VPL') {
    //             $this->nextLevelRequestId = Codemaster::getIdByCode(2201);
    //         } elseif ($this->applicantStatus == 'VL') {
    //             $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
    //         } elseif ($this->applicantStatus == 'AL') {
    //             $this->nextLevelRequestId = Codemaster::getIdByCode(2203);
    //         } elseif ($this->applicantStatus == 'RL') {
    //             $this->nextLevelRequestId = Codemaster::getIdByCode(2204);
    //         } else {
    //             $this->nextLevelRequestId = 0;
    //         }

    //         if (!empty($this->nextLevelRequestId)) {
    //             $query->where('next_level_requested_id', $this->nextLevelRequestId);
    //         }
    //     }
    //     // dd( ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
    //     // dd($query->get());

    //     return $query;
    // }

    // Using joins to optimize query and select specific columns
    // public function builder(): Builder
    // {
    //     $query = CasteModificationInfo::query()
    //         ->select([
    //             'caste_modification_infos.id',
    //             'caste_modification_infos.application_id',
    //             'caste_modification_infos.beneficiary_id',
    //             'caste_modification_infos.caste_request_type',
    //             'caste_modification_infos.next_level_requested_id',
    //             'bp.full_name as applicant_full_name',
    //             'bp.mobile_no as applicant_mobile_no',
    //             'br.full_name as father_full_name',
    //         ])
    //         ->leftJoin('lb_scheme.beneficiary_common_lists as bcl', function ($join) {
    //             $join->on('bcl.sourceable_id', '=', 'caste_modification_infos.application_id')
    //                 ->where('bcl.sourceable_type', '=', BeneficiaryPersonal::class);
    //         })
    //         ->leftJoin('lb_scheme.beneficiary_personals as bp', 'bp.application_id', '=', 'bcl.sourceable_id')
    //         ->leftJoin('lb_scheme.beneficiary_relationships as rel', function ($join) {
    //             $join->on('rel.application_id', '=', 'bp.application_id')
    //                 ->where('rel.relation_type_id', 79);
    //         })
    //         // ->leftJoin('relationships as rfirst', function($join){


    //         // })
    //         ->with([
    //             'beneficiaryCommonList' => function ($q) {
    //                 $q->select(['sourceable_id', 'sourceable_type', 'id']);
    //             },
    //             'beneficiaryCommonList.sourceable' => function ($q) {
    //                 $q->select(['application_id', 'full_name', 'beneficiary_id', 'mobile_no']);
    //             },
    //             'beneficiaryCommonList.sourceable.relationships' => function ($q) {
    //                 $q->select(['id', 'application_id', 'full_name', 'relation_type_id'])
    //                     ->where('relation_type_id', 79);
    //             },
    //             'beneficiaryCommonList.sourceable.contact' => function ($q) {
    //                 $q->select(['id', 'application_id']);
    //             },
    //         ]);

    //     // filters from your original code (kept)
    //     if (!empty($this->filter_condition) && is_array($this->filter_condition)) {
    //         $query->whereHas('beneficiaryCommonList', function ($q) {
    //             foreach ($this->filter_condition as $col => $val) {
    //                 $q->where($col, $val);
    //             }

    //         });
    //     }

    //     if (!empty($this->casteId)) {
    //         $query->where('caste_request_type', $this->casteId);
    //     }
    //     if (!empty($this->applicantStatus)) {

    //         if (!empty($this->nextLevelRequestId)) {
    //             $query->where('next_level_requested_id', $this->nextLevelRequestId);
    //         }
    //     }
    //     // dd(['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
    //     return $query;
    // }



    public function columns(): array
    {
        return [
            Column::make("ID", "id"),
            Column::make("Application Id", "application_id"),
            Column::make('Name')
                ->label(
                    fn($row) =>
                    $row->beneficiaryPersonal?->beneficiary_name ?? 'N/A'
                ),
            Column::make('Scheme')
                ->label(
                    fn($row) =>
                    $row->beneficiaryPersonal?->scheme_id
                ),

            Column::make("Father's Name")
                ->label(
                    fn($row) =>
                    $row->beneficiaryPersonal?->ben_father_name ?? 'N/A'
                ),
            //     Column::make("Actions")
            //         ->label(fn($row) => view('coulmn_button.view', [
            //             'link' => route('view-beneficiary-details', [
            //                 'application_id' => Crypt::encrypt($row->application_id)
            //             ]),
            //             'tooltip' => 'view Application',
            //         ])->render())
            //         ->html()
            //         ->hideIf(
            //             !auth()->user()?->hasAnyRole(['Verifier', 'Approver'])
            //         ),
            Column::make("Actions")
                ->label(function ($row) {
                    if (CheckAuthHelper::isCommonWorkFlow2ndStep() && $this->applicantStatus == 'PL') {
                        return view('coulmn_button.view', [
                            'link' => route('view-beneficiary-details', [
                                'application_id' => Crypt::encryptstring($row->application_id),
                                'Scheme' => Crypt::encryptstring($row->beneficiaryPersonal->scheme_id)
                            ]),
                            'tooltip' => 'View Application',
                        ])->render();
                    }

                    if (CheckAuthHelper::isOperator() && $this->applicantStatus == 'RL') {
                        return view('coulmn_button.actions', [
                            'link' => route('caste-modification.edit', [
                                'application_id' => Crypt::encryptstring($row->application_id),
                                'beneficiary_id' => Crypt::encryptstring($row->beneficiary_id),
                                'scheme_id' => Crypt::encryptstring($row->beneficiaryPersonal->scheme_id)
                            ]),
                            'tooltip' => 'Edit Application',
                        ])->render();
                    }

                    $message = $this->getStatusMessage($this->applicantStatus);
                    $colorClass = match ($this->applicantStatus) {
                        'APL' => 'bg-blue-100 text-blue-700 border-blue-300',
                        'VPL' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                        'AL'  => 'bg-green-100 text-green-700 border-green-300',
                        'RL'  => 'bg-red-100 text-red-700 border-red-300',
                        'VL'  => 'bg-emerald-100 text-emerald-700 border-emerald-300',
                        default => 'bg-gray-100 text-gray-700 border-gray-300',
                    };

                    return "<span class='px-2 py-1 text-xs font-semibold border rounded-md {$colorClass}'>
                    {$message}
                </span>";
                })
                ->html(),

        ];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('Application ID')
                ->filter(function ($query, $value) {
                    $query->where('application_id', 'ILIKE', "%{$value}%");
                }),

            TextFilter::make('Beneficiary ID')
                ->filter(function ($query, $value) {
                    $query->where('beneficiary_id', 'ILIKE', "%{$value}%");
                }),
        ];
    }

    public function render(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        if (!$this->showTable) {
            return view('livewire.caste-modification-list-table');
        }

        return parent::render();
    }

    public function exportExcel()
    {
        $data = $this->builder()->get()->map(function ($row) {
            $source = $row->beneficiaryCommonList?->sourceable;
            // dd($row->beneficiaryCommonList?->sourceable->mobile_no);
            return [
                'Application ID' => $source?->application_id ?? 'N/A',
                'Applicant Name' => $source?->full_name ?? 'N/A',
                'Father Name'    => optional(
                    $source?->relationships?->firstWhere(
                        'relation_type_id',
                        Codemaster::getIdByCode(131)
                    )
                )?->full_name ?? 'N/A',
                'DOB'            => $source?->dob ?? 'N/A',
                'Mobile'         => $row->beneficiaryCommonList?->sourceable->mobile_no ?? 'N/A',
            ];
        });

        return Excel::download(new BeneficiariesExport($data), 'applications_all.xlsx');
    }
}
