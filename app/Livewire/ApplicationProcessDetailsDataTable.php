<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
use App\Helpers\EncryptionArray;
use App\Exports\BeneficiariesExport;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\BeneficiaryPersonal;
use App\Models\FaultyBeneficiaryPersonal;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Actions\Action;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use App\Models\Codemaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryPersonalDetail;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use App\Models\BenRejectDetails;
use App\Models\DraftBeneficiaryBank;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryRelationship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WorkflowService;

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
    public $sameLabelRoleId, $nextLabelRoleId;
    public $isFinal;
    public function mount($schemeId = null, WorkflowService $workflowService): void
    {
        $this->schemeId = $schemeId;
        $this->isFinal = 1;
        $labelRoles = $workflowService->getLabelRoles($schemeId);
        if ($labelRoles) {
            $this->sameLabelRoleId = $labelRoles->same_label_role_id;
            $this->nextLabelRoleId = $labelRoles->next_label_role_id;
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
        // dd($filters);
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

        // Bulk Verify
        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'verification', true) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'verification', true)) && CheckAuthHelper::isCommmonVerifier()
        ) {
            $actions['bulkverify'] = 'Verify';
        }

        // Bulk Approve
        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'approver', true) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'approver', true)) && CheckAuthHelper::isCommonApprover()
        ) {
            $actions['bulkapprove'] = 'Approve';
        }

        // Bulk Reject
        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'reject', true) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'reject', true)) && CheckAuthHelper::isCommonWorkFlow2ndStep()
        ) {
            $actions['bulkreject'] = 'Reject';
        }

        // Bulk Revert
        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'revert', true) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'revert', true)) && CheckAuthHelper::isCommonWorkFlow2ndStep()
        ) {
            $actions['bulkrevert'] = 'Revert';
        }
        $actions['bulkverify'] = 'Verify';
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
                    return view('coulmn_button.view', [
                        'link' => route('draft-application.view', Crypt::encryptString($row->application_id)),
                        'tooltip' => 'View Application',
                    ])->render();
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        $query = BeneficiaryPersonalDetail::query()->select('application_id', 'beneficiary_id', 'scheme_id', 'beneficiary_name', 'ben_father_name', 'dob', 'application_type')
            ->whereIn('is_clean', [1, 2])
            ->where('next_level_role_id', $this->sameLabelRoleId)
            ->where('scheme_id', $this->schemeId)
            ->where('is_final', $this->isFinal);
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
        // dd($query->get());
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
        $this->revertrejectCauses = Codemaster::where('code', 12)->first()->children()->get();
        $this->revertrejectAction = $action;
        $this->dispatch('open-bulk-revert-modal', action: $action, revertrejectCauses: $this->revertrejectCauses);
    }
    #[On('confirm-bulk-revert')]
    public function confirmBulkRevert($validated, WorkflowService $workflowService)
    {
        if ($this->revertrejectAction === 'revert') {
            $this->nextLabelRoleId = $workflowService->getLabelRoles($this->schemeId, 1)->same_label_role_id;
        } elseif ($this->revertrejectAction === 'reject') {
            $this->nextLabelRoleId = -100;
        }
        $ids = $this->getSelected();
        $select_lgd = session('lgd_session');
        $user_id = Crypt::decryptString($select_lgd['role_id']);
        if ($this->revertrejectAction === 'revert') {
            // $user = auth()->user();
            /*  if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
                $next_level_role_id = Codemaster::getIdByCode(22);
            }
            if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
                $next_level_role_id = Codemaster::getIdByCode(21);
            } */
            $next_level_role_id = Codemaster::getIdByCode(21);
            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->where($this->filter_condition)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);
                    $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $id)->value('beneficiary_id');
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $id;
                    $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = $next_level_role_id;
                    $AcceptRejectInfo->revert_reason_cause_id = $validated['cause'];
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
            $this->dispatch('toastr', [
                'type' => 'warning',
                'message' => 'All applications reverted successfully!'
            ]);
            $this->clearSelected();
            $this->dispatch('actionPerformedAndRedirect');
        } elseif ($this->revertrejectAction === 'reject') {
            $ids = $this->getSelected();
            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->where($this->filter_condition)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                        'is_clean' => 10,
                    ]);
                    $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $id)->value('beneficiary_id');
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $id;
                    $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = Codemaster::getIdByCode(-1);
                    $AcceptRejectInfo->revert_reason_cause_id = $validated['cause'];
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'All applications rejected successfully!'
            ]);
            $this->clearSelected();
            $this->dispatch('actionPerformedAndRedirect');
        } elseif ($this->revertrejectAction === 'verification') {

            $ids = $this->getSelected();

            if (empty($ids)) {
                return;
            }

            // get application types for selected rows
            $applicationTypes = BeneficiaryPersonalDetail::whereIn('application_id', $ids)
                ->pluck('application_type', 'application_id');

            $processed = 0;
            $approverRoleId = Codemaster::getIdByCode(23);

            foreach ($ids as $id) {

                $applicationType = $applicationTypes[$id] ?? 0;

                $capacityCheck = \App\Helpers\SchemeCapacityHelper::checkBulk(
                    $this->schemeId,
                    1,
                    $applicationType
                );

                if (!$capacityCheck || !$capacityCheck['is_processed']) {

                    $this->dispatch('toastr', [
                        'type' => 'error',
                        'message' => 'Capacity exceeded for ' . ($capacityCheck['model'] ?? 'Scheme')
                    ]);

                    break;
                }

                DB::beginTransaction();

                try {

                    $application = BeneficiaryPersonalDetail::where('application_id', $id)->first();

                    if (!$application) {
                        DB::rollBack();
                        continue;
                    }

                    BeneficiaryPersonalDetail::where('application_id', $id)->update([
                        'next_level_role_id' => $this->nextLabelRoleId
                    ]);

                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $application->application_id;
                    $AcceptRejectInfo->beneficiary_id = $application->beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = $approverRoleId;
                    $AcceptRejectInfo->revert_reason_cause_id = null;
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;

                    $AcceptRejectInfo->save();

                    DB::commit();

                    $processed++;
                } catch (\Exception $e) {

                    DB::rollBack();
                    throw $e;
                }
            }

            if ($processed == 0) {

                // $this->dispatch('toastr', [
                //     'type' => 'error',
                //     'message' => 'Capacity exceeded. No application verified.'
                // ]);
            } else {

                $this->dispatch('toastr', [
                    'type' => 'success',
                    'message' => $processed . ' application verified successfully!'
                ]);
            }

            $this->clearSelected();
            $this->dispatch('actionPerformedAndRedirect');
        } elseif ($this->revertrejectAction === 'approver') {
            $ids = $this->getSelected();

            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    BeneficiaryPersonalDetail::where('application_id', $id)->where($this->filter_condition)->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                        'is_clean' => 1,
                    ]);
                    $beneficiary_id = BeneficiaryPersonalDetail::where('application_id', $id)->value('beneficiary_id');
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $id;
                    $AcceptRejectInfo->beneficiary_id = $beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->scheme_id = $this->schemeId;
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = Codemaster::getIdByCode(0);
                    $AcceptRejectInfo->revert_reason_cause_id = null;
                    $AcceptRejectInfo->revert_reason_remarks = $validated['remark'];
                    $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null;
                    $AcceptRejectInfo->save();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'All applications approved successfully!'
            ]);
            $this->clearSelected();
            $this->dispatch('actionPerformedAndRedirect');
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
