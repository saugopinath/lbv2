<?php

namespace App\Livewire;

use App\Helpers\EncryptionArray;
use App\Exports\BeneficiariesExport;
use App\Helpers\CheckAuthHelper;
use App\Helpers\SchemeCapacityHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use App\Models\Codemaster;
use Carbon\Carbon;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonalDetail;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WorkflowService;
use App\Models\DynamicWorkflowSchemeModule;

class ApplicationProcessDetailsDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';
    public $schemeId;

    public $district_id, $rural_urban, $blockurban, $gp_ward, $next_level_role_id, $revertrejectAction, $revertrejectCauses, $sub_div;
    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public $sameLevelRoleId, $nextLevelRoleId;
    public array $sameLevelRoleIds = [];
    public $isFinal = 1;
    public $moduleCode;
    public $schemeModuleId;
    public array $stepTargets = [];

    public function mount($schemeId = null, ?WorkflowService $workflowService = null, $moduleCode = null, $isFinal = 1): void
    {
        $workflowService = $workflowService ?? app(WorkflowService::class);
        $this->schemeId = $schemeId;
        $this->moduleCode = $moduleCode;
        $this->isFinal = $isFinal ?? 1;

        if ($this->schemeId && $this->moduleCode) {
            $this->schemeModuleId = DynamicWorkflowSchemeModule::where('scheme_id', $this->schemeId)
                ->where('main_module_code', $this->moduleCode)
                ->where('is_disabled', false)
                ->value('id');
        }

        // Resolves all matching step targets (module_id, rank, final/intermediate) across active modules
        $this->stepTargets = $workflowService->getWorkflowStepTargets($schemeId, $this->schemeModuleId);
        $this->sameLevelRoleIds = array_values(array_unique(array_column($this->stepTargets, 'rank')));

        $levelRoles = $workflowService->getLevelRoles($schemeId, null, $this->schemeModuleId, $this->moduleCode);
        if ($levelRoles) {
            $this->sameLevelRoleId = $levelRoles->rank ?? $levelRoles->same_level_role_id;
            $this->nextLevelRoleId = $levelRoles->next_level_role_id;
        }

        if (empty($this->sameLevelRoleIds) && $this->sameLevelRoleId) {
            $this->sameLevelRoleIds = [(int) $this->sameLevelRoleId];
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
    }
    public function filtersApplied($filters)
    {
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
        $this->sub_div = $filters['subdivision_id'];
    }
    public function configure(): void
    {
        $this->setPrimaryKey('application_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();



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
    public function bulkActions(): array
    {
        $actions = [
            'exportSelected' => 'Export',
        ];

        if (empty($this->schemeId)) {
            return $actions;
        }

        $workflowService = app(WorkflowService::class);
        $targets = !empty($this->stepTargets) ? $this->stepTargets : $workflowService->getWorkflowStepTargets($this->schemeId, $this->schemeModuleId);

        $hasIntermediate = false;
        $hasFinal = false;
        $hasFirst = false;

        if (!empty($targets)) {
            foreach ($targets as $t) {
                if ($t['is_final_step']) {
                    $hasFinal = true;
                } else {
                    $hasIntermediate = true;
                }
                if ($t['is_first_step']) {
                    $hasFirst = true;
                }
            }
        } else {
            $data = $workflowService->getLevelRoles($this->schemeId, null, $this->schemeModuleId, $this->moduleCode);
            if ($data) {
                if ($data->is_final_step) {
                    $hasFinal = true;
                } else {
                    $hasIntermediate = true;
                }
                if ($data->is_first_step) {
                    $hasFirst = true;
                }
            }
        }

        $canVerify = WorkFlowPermissionHelper::canBulkActionAllow(1, 'verification', true, $this->schemeId) ||
            WorkFlowPermissionHelper::canBulkActionAllow(2, 'verification', true, $this->schemeId);

        $canApprove = WorkFlowPermissionHelper::canBulkActionAllow(1, 'approver', true, $this->schemeId) ||
            WorkFlowPermissionHelper::canBulkActionAllow(2, 'approver', true, $this->schemeId);

        if (($canVerify || $canApprove) && $hasIntermediate && $hasFinal) {
            $actions['bulkapprove'] = 'Approve / Forward';
        } elseif (($canApprove || $canVerify) && $hasFinal) {
            $actions['bulkapprove'] = 'Approve';
        } elseif (($canVerify || $canApprove) && $hasIntermediate) {
            $actions['bulkverify'] = 'Verify / Forward';
        }

        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'reject', true, $this->schemeId) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'reject', true, $this->schemeId)) && (!$hasFirst || ($hasFinal && $hasFirst))
        ) {
            $actions['bulkreject'] = 'Reject';
        }

        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'revert', true, $this->schemeId) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'revert', true, $this->schemeId)) && (!$hasFirst || ($hasFinal && $hasFirst))
        ) {
            $actions['bulkrevert'] = 'Revert';
        }

        return $actions;
    }

    public function updatedSearch($value): void
    {
        $this->setSearch($value);
        $this->resetPage();
    }
    public function updatedPerPage($value): void
    {
        $this->perPage = (int) $value;
        $this->setPerPage((int) $value);
        $this->resetPage();
    }

    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->application_id ?? 'N/A'),
            Column::make("Application Type", "application_type")
                ->label(fn($row) => $row->application_type ?? 'N/A'),
            Column::make("Area / Type")
                ->label(function ($row) {
                    $ruralUrban = $row->contact?->rural_urban;
                    if ($ruralUrban == 2) {
                        $blockName = $row->contact?->block?->name;
                        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Rural' . ($blockName ? ' (' . e($blockName) . ')' : '') . '</span>';
                    } elseif ($ruralUrban == 1) {
                        $muniName = $row->contact?->municipality?->name;
                        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">Urban' . ($muniName ? ' (' . e($muniName) . ')' : '') . '</span>';
                    }
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-50 border border-gray-200">General</span>';
                })
                ->html(),
            Column::make("Applicant Name")
                ->label(fn($row) => $row->beneficiary_name ?? 'N/A'),

            Column::make("Father's Name")
                ->label(fn($row) => $row->ben_father_name ?? 'N/A'),

            Column::make("Date of Birth")
                ->label(fn($row) => $row->dob ?? 'N/A'),
            Column::make("Age", "age")
                ->label(fn($row) => Carbon::parse($row->dob)->age
                    ?? 'N/A'),
            Column::make("Actions")
                ->label(function ($row) {
                    $routeParams = ['application_id' => Crypt::encryptString($row->application_id)];
                    if (!empty($this->moduleCode)) {
                        $routeParams['module'] = Crypt::encryptString($this->moduleCode);
                    }
                    return view('coulmn_button.view', [
                        'link' => route('draft-application.view', $routeParams),
                        'tooltip' => 'View Application',
                    ])->render();
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        $query = BeneficiaryPersonalDetail::query()
            ->with(['contact.block:id,name', 'contact.municipality:id,name'])
            ->select('application_id', 'beneficiary_id', 'scheme_id', 'scheme_module_id', 'beneficiary_name', 'ben_father_name', 'dob', 'application_type')
            ->whereIn('is_clean', [1, 2])
            ->where('scheme_id', $this->schemeId)
            ->where('is_final', $this->isFinal);

        if (!empty($this->stepTargets)) {
            $query->where(function ($q) {
                foreach ($this->stepTargets as $target) {
                    $q->orWhere(function ($sub) use ($target) {
                        if (!empty($target['scheme_module_id'])) {
                            $sub->where('scheme_module_id', $target['scheme_module_id'])
                                ->where('next_level_role_id', $target['rank']);
                        } else {
                            $sub->where('next_level_role_id', $target['rank']);
                        }
                    });
                }
            });
        } elseif (!empty($this->sameLevelRoleIds)) {
            $query->whereIn('next_level_role_id', $this->sameLevelRoleIds);
        } elseif ($this->sameLevelRoleId) {
            $query->where('next_level_role_id', $this->sameLevelRoleId);
        }

        if (!empty($this->filter_condition)) {
            $query->where($this->filter_condition);
        }
        if ($this->district_id || $this->sub_div || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null,
                $this->sub_div ? (int) $this->sub_div : null
            );
        }
        $this->dispatch('hideLoader');
        return $query;
    }

    public function bulkverify()
    {
        $this->handleBulkAction('verification');
    }

    public function bulkapprove()
    {
        $this->handleBulkAction('approver');
    }

    public function bulkrevert()
    {
        $this->handleBulkAction('revert');
    }

    public function bulkreject()
    {
        $this->handleBulkAction('reject');
    }

    public function handleBulkAction($action)
    {
        $this->revertrejectCauses = Codemaster::where('code', 12)->first()?->children()->select('id', 'name')->get()?->toArray() ?? [];
        $this->revertrejectAction = $action;

        $ids = $this->getSelected();
        $warningMessage = null;

        if (in_array($action, ['verification', 'approver']) && !empty($ids)) {
            $selectedRecords = BeneficiaryPersonalDetail::whereIn('application_id', $ids)
                ->select('application_id', 'scheme_module_id', 'scheme_id', 'next_level_role_id')
                ->get();

            $workflowService = app(WorkflowService::class);
            $hasFinal = false;
            $hasIntermediate = false;
            $stepCache = [];

            foreach ($selectedRecords as $record) {
                $moduleId = $record->scheme_module_id;
                $step = $stepCache[$moduleId] ??= $workflowService->getLevelRoles($this->schemeId, null, $moduleId);
                if ($step) {
                    if ($step->is_final_step) {
                        $hasFinal = true;
                    } else {
                        $hasIntermediate = true;
                    }
                }
            }

            if ($hasFinal && $hasIntermediate) {
                $warningMessage = "Selected applications are at different workflow stages (some require Final Approval, while others forward to Recommender). Proceeding will intelligently progress each application to its respective next stage.";
            }
        }

        $this->dispatch('open-bulk-revert-modal', action: $action, revertrejectCauses: $this->revertrejectCauses, warningMessage: $warningMessage);
    }

    #[On('confirm-bulk-revert')]
    public function confirmBulkRevert($validated, WorkflowService $workflowService)
    {
        $ids = $this->getSelected();
        if (empty($ids)) {
            return;
        }

        $action = $this->revertrejectAction;

        DB::beginTransaction();
        try {
            $records = BeneficiaryPersonalDetail::whereIn('application_id', $ids)
                ->select('application_id', 'beneficiary_id', 'scheme_id', 'scheme_module_id', 'next_level_role_id', 'is_clean')
                ->get();

            if ($records->isEmpty()) {
                DB::rollBack();
                return;
            }

            // Batch-query latest parent IDs from accept_reject_infos in 1 query instead of N queries
            $appIds = $records->pluck('application_id')->toArray();
            $latestParentIds = AcceptRejectInfo::whereIn('application_id', $appIds)
                ->select('application_id', DB::raw('MAX(id) as max_id'))
                ->groupBy('application_id')
                ->pluck('max_id', 'application_id')
                ->toArray();

            $userId = Auth::id();
            $ipAddress = request()->ip();
            $browser = request()->header('User-Agent');
            $nowDate = Carbon::now()->toDateString();

            if ($action === 'revert') {
                $revertOpType = Codemaster::getIdByCode(21);
                $firstStepsCache = [];

                foreach ($records as $record) {
                    $moduleId = $record->scheme_module_id;
                    $firstStep = $firstStepsCache[$moduleId] ??= \App\Models\WorkflowsteproleMapping::select('id', 'scheme_id', 'module_id', 'rank', 'same_level_role_id')
                        ->where('scheme_id', $this->schemeId)
                        ->when($moduleId, fn($q) => $q->where('module_id', $moduleId))
                        ->orderBy('rank', 'asc')
                        ->first();
                    $targetNextRoleId = $firstStep ? ($firstStep->rank ?? $firstStep->same_level_role_id) : 10;

                    $record->update([
                        'next_level_role_id' => $targetNextRoleId,
                    ]);

                    AcceptRejectInfo::create([
                        'application_id'         => $record->application_id,
                        'beneficiary_id'         => $record->beneficiary_id,
                        'ip_address'             => $ipAddress,
                        'scheme_id'              => $this->schemeId,
                        'user_id'                => $userId,
                        'browser'                => $browser,
                        'op_type'                => $revertOpType,
                        'revert_reason_cause_id' => $validated['cause'] ?? null,
                        'revert_reason_remarks'  => $validated['remark'] ?? null,
                        'parent_id'              => $latestParentIds[$record->application_id] ?? null,
                    ]);
                }

                DB::commit();
                $this->dispatch('toastr', [
                    'type' => 'warning',
                    'message' => 'All applications reverted successfully!'
                ]);
            } elseif ($action === 'reject') {
                $rejectOpType = Codemaster::getIdByCode(-1);

                foreach ($records as $record) {
                    $record->update([
                        'next_level_role_id' => -100,
                        'is_clean'           => 10,
                        'rejection_date'     => $nowDate,
                    ]);

                    AcceptRejectInfo::create([
                        'application_id'         => $record->application_id,
                        'beneficiary_id'         => $record->beneficiary_id,
                        'ip_address'             => $ipAddress,
                        'scheme_id'              => $this->schemeId,
                        'user_id'                => $userId,
                        'browser'                => $browser,
                        'op_type'                => $rejectOpType,
                        'revert_reason_cause_id' => $validated['cause'] ?? null,
                        'revert_reason_remarks'  => $validated['remark'] ?? null,
                        'parent_id'              => $latestParentIds[$record->application_id] ?? null,
                    ]);
                }

                DB::commit();
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => 'All applications rejected successfully!'
                ]);
            } elseif (in_array($action, ['verification', 'approver'])) {
                $finalOpType = Codemaster::getIdByCode(0);
                $verifyOpType = Codemaster::getIdByCode(23);
                $stepCache = [];

                foreach ($records as $record) {
                    $moduleId = $record->scheme_module_id;
                    $step = $stepCache[$moduleId] ??= $workflowService->getLevelRoles($this->schemeId, null, $moduleId);
                    $isFinal = $step ? (bool) $step->is_final_step : ($action === 'approver');
                    $nextRole = $step ? $step->next_level_role_id : 0;

                    if ($isFinal) {
                        $record->update([
                            'next_level_role_id' => $nextRole ?? $record->next_level_role_id,
                            'is_clean'           => 1,
                            'approval_date'      => $nowDate,
                        ]);
                        $opType = $finalOpType;
                    } else {
                        $record->update([
                            'next_level_role_id' => $nextRole,
                            'verification_date'  => $nowDate,
                        ]);
                        $opType = $verifyOpType;
                    }

                    AcceptRejectInfo::create([
                        'application_id'        => $record->application_id,
                        'beneficiary_id'        => $record->beneficiary_id,
                        'ip_address'            => $ipAddress,
                        'scheme_id'             => $this->schemeId,
                        'user_id'               => $userId,
                        'browser'               => $browser,
                        'op_type'               => $opType,
                        'revert_reason_remarks' => $validated['remark'] ?? null,
                        'parent_id'             => $latestParentIds[$record->application_id] ?? null,
                    ]);
                }

                DB::commit();
                $this->dispatch('toastr', [
                    'type' => 'success',
                    'message' => 'Applications processed successfully!'
                ]);
            }

            $this->clearSelected();
            $this->dispatch('actionPerformedAndRedirect');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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
