<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
use App\Helpers\EncryptionArray;
use App\Exports\BeneficiariesExport;
use App\Helpers\CheckAuthHelper;
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

class CmoWorkFlowDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';
    public $greCat, $chemeName;
    public $district_id, $rural_urban, $blockurban, $gp_ward, $next_level_role_id, $revertrejectAction, $revertrejectCauses, $sub_div, $district;
    // protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public $process_type;

    protected $listeners = ['processTypeChanged' => 'updateProcessType'];
    public function updateProcessType($type)
    {
        $this->district = $type['district'];
        $process_type = $type['process_type'];
        $user = auth()->user();
        if ($process_type) {
            if ($process_type == Codemaster::getIdByCode(3302) && (CheckAuthHelper::isCommonApprover())) {
                $this->process_type = [Codemaster::getIdByCode(3302), Codemaster::getIdByCode(3304)];
            } else {
                $this->process_type = [$process_type];
            }
        }
    }

    public function greCat($schemeId)
    {
        if ($schemeId = 20) {
            return 127;
        }
    }

    public function mount($schemeId = null, $schemeName = null): void
    {
        $this->greCat = $this->greCat($schemeId);
        $user = auth()->user();
        if (CheckAuthHelper::isCommmonVerifier()) {
            $this->process_type = [Codemaster::getIdByCode(3301)];
        } elseif (CheckAuthHelper::isCommonOperator()) {
            $this->process_type = [Codemaster::getIdByCode(3304)];
        } elseif (CheckAuthHelper::isCommonApprover()) {
            $this->process_type = [Codemaster::getIdByCode(3302), Codemaster::getIdByCode(3304)];
        } elseif (CheckAuthHelper::isCommonHOD()) {
            $this->process_type = [Codemaster::getIdByCode(3303)];
        }

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
    public function bulkActions(): array
    {
        if ($this->builder()->count() == 0) {
            return [];
        }
        $user = auth()->user();
        $actions = [];
        if (CheckAuthHelper::isCommonHOD() && $this->process_type == [Codemaster::getIdByCode(3303)]) {
            $actions['bulkpush'] = 'Push To CMO';
        }
        return $actions;
    }
    public function configure(): void
    {
        $this->setPrimaryKey('grievance_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            // ->setBulkActionsEnabled()
        ;

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
    public function filters(): array
    {
        return [
            TextFilter::make('Application ID')
                ->filter(function ($query, $value) {
                    $query->whereHas('sourceable', function ($q) use ($value) {
                        $q->where('application_id', 'ILIKE', "%{$value}%");
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

    public function columns(): array
    {
        return [
            Column::make("Grievance ID", "grievance_id")
                ->label(fn($row) => $row->grievance_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('sourceable', function ($q) use ($searchTerm) {
                        $q->where('grievance_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make("Caller Name", "full_name")
                ->label(fn($row) => $row->applicant_name ?? 'N/A'),

            Column::make("Caller Mobile No", "caller_mobile_no")
                ->label(fn($row) => $row->pri_cont_no ?? 'N/A'),
            Column::make("CMO Received Date(YYYY-MM-DD)", "CMO_Received_Date(YYYY-MM-DD)")
                ->label(fn($row) => Carbon::parse($row->grievance_generate_date)->toDateString() ?? 'N/A'),
            // $columns[] = Column::make("Action")
            //     ->label(function ($row) {
            //         return view('coulmn_button.view', [
            //             // 'link' => route('cmo-grievance-find', Crypt::encryptString($row->grievance_id)),
            //             'link' => route('cmo-grievance-find') . '?id=' . Crypt::encryptString($row->grievance_id),
            //             'tooltip' => 'Find',
            //         ])->render();
            //     })
            //     ->html(),
            Column::make("Action")
                ->label(function ($row) {
                    $user = auth()->user();
                    $processType = $this->process_type;
                    $canEdit = false;
                    if (
                        ((CheckAuthHelper::isCommmonVerifier() && in_array(Codemaster::getIdByCode(3301), $processType)) ||
                            (CheckAuthHelper::isCommonApprover() &&
                                (in_array(Codemaster::getIdByCode(3302), $processType) || in_array(Codemaster::getIdByCode(3304), $processType))
                            ) ||
                            (CheckAuthHelper::isCommonHOD() && in_array(Codemaster::getIdByCode(3303), $processType)) ||
                            (CheckAuthHelper::isCommonOperator() && in_array(Codemaster::getIdByCode(3304), $processType)) && $row->is_mark != 1)
                    ) {
                        $canEdit = true;
                    }
                    if (!$canEdit) {
                        return 'N/A';
                    }
                    if (CheckAuthHelper::isCommonFindUser()) {
                        $routeName = 'cmo-grievance-find';
                    } elseif (CheckAuthHelper::isCommonOperator()) {
                        $routeName = 'form';
                    } else {
                        return '';
                    }
                    $link = route($routeName) . '?id=' . Crypt::encryptString($row->grievance_id);
                    return view('coulmn_button.actions', [
                        'link' => $link,
                        'tooltip' => 'Edit',
                    ])->render();
                })
                ->html(),
        ];
    }
    public function builder(): Builder
    {
        $query = CmoSmData::where('grievance_category', $this->greCat);
        if (!empty($this->process_type)) {
            $query->wherein('redressed_status', $this->process_type);
        }
        if (!empty($this->filter_condition)) {
            foreach ($this->filter_condition as $column => $value) {
                if (!empty($value)) {
                    $query->where($column, $value);
                }
            }
        }
        if ($this->district) {
            $query->where('lb_dist_code', $this->district);
        }
        // dd($this->district,$this->filter_condition,$this->process_type);
        return $query;
    }
    public function bulkpush()
    {
        DB::beginTransaction();
        try {
        $ids = $this->getSelected();
        foreach ($ids as $grievance_id) {
            $CmoSmData = CmoSmData::where('grievance_id', $grievance_id)
                ->where('is_processed', 2)
                ->first();
            $comment = $CmoSmData->remarks ?? '';
            $comment = preg_replace('/\s+/', ' ', preg_replace('/[^a-zA-Z0-9 ]/', '', str_replace(["\t", "\n", "\r"], ' ', $comment)));
            $comment = trim($comment);
            $data = [
                "data" => [
                    [
                        "position_id" => 1,
                        "grievance_status" => "GM014",
                        "grievance_id" => null,
                        "comment" => $comment,
                        "bulk_grivance_id" => [$CmoSmData->grievance_id],
                        "assign_comment" => null,
                        "action_proposed" => null,
                        "urgency_flag" => null,
                        "addl_doc_id" => [],
                        "atn_id" => (int) $CmoSmData->atr_type,
                        "atn_reason_master_id" => null,
                        "action_taken_note" => $CmoSmData->atr_desc,
                        "contact_date" => null,
                        "tentative_date" => null,
                        "atr_doc_id" => [],
                        "action" => "TA"
                    ]
                ]
            ];
            $cmoAuthenticationService = app(\App\Interfaces\CmoAuthenticationInterface::class);
            $data = $cmoAuthenticationService->submitNewATR($data);
            $cmo_data = json_decode($data->getContent(), true);
            $message = $cmo_data['message'];
            $status = $cmo_data['status'];
            if ($status == 200 && $message == 'Grievance status updated successfully') {
                $CmoSmData->redressed_status = Codemaster::getIdByCode(3305);
                $CmoSmData->is_processed = 3;
                $CmoSmData->response_back_by = Auth::id();
                $CmoSmData->response_back_date = date('Y-m-d H:i:s');
                $CmoSmData->save();
            }
        }
        DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'All Grievance Are Pushed Successfully'
        ]);
        $this->clearSelected();
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
