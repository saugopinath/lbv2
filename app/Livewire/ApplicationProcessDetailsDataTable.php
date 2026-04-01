<?php

namespace App\Livewire;

use App\Helpers\EncryptionArray;
use App\Helpers\SchemeCapacityHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use App\Services\TableExportService;
use App\Services\WorkflowService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ApplicationProcessDetailsDataTable extends DataTableComponent
{
    public ?int $perPage = 5;

    public string $reportType;

    public string $login_type = '';

    public string $search = '';

    public $schemeId;

    public $district_id;

    public $rural_urban;

    public $blockurban;

    public $gp_ward;

    public $next_level_role_id;

    public $revertrejectAction;

    public $revertrejectCauses;

    public $sub_div;

    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode;

    public $loginSubdivisionCode;

    public $loginBlockCode;

    public array $filter_condition = [];

    public $sameLabelRoleId;

    public $nextLabelRoleId;

    public $isFinal;

    public function mount($schemeId, WorkflowService $workflowService): void
    {
        $this->schemeId = $schemeId;
        $this->isFinal = 1;
        $labelRoles = $workflowService->getLabelRoles($schemeId);
        if ($labelRoles) {
            $this->sameLabelRoleId = $labelRoles->same_label_role_id;
            $this->nextLabelRoleId = $labelRoles->next_label_role_id;
        }

        $select_lgd = session('lgd_session');

        if (! empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (! empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (! empty($select_lgd['subdivision_id'])) {
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

    public function bulkActions(): array
    {
        $actions = [
            // 'exportExcel' => 'Export',
        ];
        $workflowService = app(WorkflowService::class);
        $data = $workflowService->getLabelRoles($this->schemeId);
        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'verification', true, $this->schemeId) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'verification', true, $this->schemeId)) && ((! $data->is_final_step && ! $data->is_first_step) || ($data->is_final_step && $data->is_first_step))
        ) {
            $actions['bulkverify'] = $data->workflowstep->label;
        }

        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'approver', true, $this->schemeId) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'approver', true, $this->schemeId)) && $data->is_final_step
        ) {
            $actions['bulkapprove'] = $data->workflowstep->label;
        }

        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'reject', true, $this->schemeId) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'reject', true, $this->schemeId)) && (! $data->is_first_step || ($data->is_final_step && $data->is_first_step))
        ) {
            $actions['bulkreject'] = 'Reject';
        }

        if (
            (WorkFlowPermissionHelper::canBulkActionAllow(1, 'revert', true, $this->schemeId) ||
                WorkFlowPermissionHelper::canBulkActionAllow(2, 'revert', true, $this->schemeId)) && (! $data->is_first_step || ($data->is_final_step && $data->is_first_step))
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
            Column::make('Application ID', 'application_id')
                ->label(fn ($row) => $row->application_id ?? 'N/A'),
            Column::make('Application Type', 'application_type')
                ->label(fn ($row) => $row->application_type ?? 'N/A'),
            Column::make('Applicant Name')
                ->label(fn ($row) => $row->beneficiary_name ?? 'N/A'),

            Column::make("Father's Name")
                ->label(fn ($row) => $row->ben_father_name ?? 'N/A'),

            Column::make('Date of Birth')
                ->label(fn ($row) => $row->dob ?? 'N/A'),
            Column::make('Age', 'age')
                ->label(fn ($row) => Carbon::parse($row->dob)->age
                    ?? 'N/A'),
            Column::make('Actions')
                ->label(function ($row) {
                    return view('coulmn_button.view', [
                        'link' => route('draft-application.view', ['application_id' => Crypt::encryptString($row->application_id)]),
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
        if (! empty($this->filter_condition)) {
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

            $ids = $this->getSelected();

            if (empty($ids)) {
                return;
            }

            $next_level_role_id = Codemaster::getIdByCode(21);

            DB::beginTransaction();

            try {

                $records = BeneficiaryPersonalDetail::whereIn('application_id', $ids)
                    ->select('application_id', 'beneficiary_id', 'next_level_role_id', 'is_clean')
                    ->get();

                foreach ($records as $record) {

                    $record->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);

                    $parentId = AcceptRejectInfo::where('application_id', $record->application_id)
                        ->latest('id')
                        ->value('id');

                    AcceptRejectInfo::create([
                        'application_id' => $record->application_id,
                        'beneficiary_id' => $record->beneficiary_id,
                        'ip_address' => request()->ip(),
                        'scheme_id' => $this->schemeId,
                        'user_id' => Auth::id(),
                        'browser' => request()->header('User-Agent'),
                        'op_type' => $next_level_role_id,
                        'revert_reason_cause_id' => $validated['cause'],
                        'revert_reason_remarks' => $validated['remark'],
                        'parent_id' => $parentId,
                    ]);
                }

                DB::commit();

                $this->dispatch('toastr', [
                    'type' => 'warning',
                    'message' => 'All applications reverted successfully!',
                ]);

                $this->clearSelected();
                $this->dispatch('actionPerformedAndRedirect');
            } catch (\Throwable $e) {

                DB::rollBack();
                throw $e;
            }
        } elseif ($this->revertrejectAction === 'reject') {

            $ids = $this->getSelected();

            if (empty($ids)) {
                return;
            }

            DB::beginTransaction();

            try {

                $records = BeneficiaryPersonalDetail::whereIn('application_id', $ids)
                    ->select('application_id', 'beneficiary_id', 'next_level_role_id', 'is_clean')
                    ->get();

                foreach ($records as $record) {

                    $record->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                        'is_clean' => 10,
                    ]);

                    $parentId = AcceptRejectInfo::where('application_id', $record->application_id)
                        ->latest('id')
                        ->value('id');

                    AcceptRejectInfo::create([
                        'application_id' => $record->application_id,
                        'beneficiary_id' => $record->beneficiary_id,
                        'ip_address' => request()->ip(),
                        'scheme_id' => $this->schemeId,
                        'user_id' => Auth::id(),
                        'browser' => request()->header('User-Agent'),
                        'op_type' => Codemaster::getIdByCode(-1),
                        'revert_reason_cause_id' => $validated['cause'],
                        'revert_reason_remarks' => $validated['remark'],
                        'parent_id' => $parentId,
                    ]);
                }

                DB::commit();

                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => 'All applications rejected successfully!',
                ]);

                $this->clearSelected();
                $this->dispatch('actionPerformedAndRedirect');
            } catch (\Throwable $e) {

                DB::rollBack();
                throw $e;
            }
        } elseif ($this->revertrejectAction === 'verification') {
            $ids = $this->getSelected();
            if (empty($ids)) {
                return;
            }
            $check = SchemeCapacityHelper::checkBulk($this->schemeId, $this->nextLabelRoleId, $ids);
            if (! $check['is_processed']) {
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => "Capacity exceeded for {$check['model']}! Available: {$check['remaining_capacity']}",
                ]);

                return;
            }
            DB::beginTransaction();
            try {
                $records = BeneficiaryPersonalDetail::whereIn('application_id', $ids)
                    ->select('application_id', 'beneficiary_id', 'next_level_role_id', 'is_clean')
                    ->get();

                foreach ($records as $record) {

                    $record->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                    ]);

                    $parentId = AcceptRejectInfo::where('application_id', $record->application_id)
                        ->latest('id')
                        ->value('id');

                    AcceptRejectInfo::create([
                        'application_id' => $record->application_id,
                        'beneficiary_id' => $record->beneficiary_id,
                        'ip_address' => request()->ip(),
                        'scheme_id' => $this->schemeId,
                        'user_id' => Auth::id(),
                        'browser' => request()->header('User-Agent'),
                        'op_type' => Codemaster::getIdByCode(23),
                        'revert_reason_remarks' => $validated['remark'] ?? null,
                        'parent_id' => $parentId,
                    ]);
                }
                DB::commit();
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Processed!']);
                $this->clearSelected();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } elseif ($this->revertrejectAction === 'approver') {

            $ids = $this->getSelected();

            if (empty($ids)) {
                return;
            }

            $check = SchemeCapacityHelper::checkBulk(
                $this->schemeId,
                $this->nextLabelRoleId,
                $ids
            );

            if (! $check['is_processed']) {
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => "Capacity exceeded for {$check['model']}! Available: {$check['remaining_capacity']}",
                ]);

                return;
            }

            DB::beginTransaction();

            try {

                $records = BeneficiaryPersonalDetail::whereIn('application_id', $ids)
                    ->select('application_id', 'beneficiary_id', 'next_level_role_id', 'is_clean')
                    ->get();

                foreach ($records as $record) {

                    $record->update([
                        'next_level_role_id' => $this->nextLabelRoleId,
                        'is_clean' => 1,
                    ]);

                    $parentId = AcceptRejectInfo::where('application_id', $record->application_id)
                        ->latest('id')
                        ->value('id');

                    AcceptRejectInfo::create([
                        'application_id' => $record->application_id,
                        'beneficiary_id' => $record->beneficiary_id,
                        'ip_address' => request()->ip(),
                        'scheme_id' => $this->schemeId,
                        'user_id' => Auth::id(),
                        'browser' => request()->header('User-Agent'),
                        'op_type' => Codemaster::getIdByCode(0),
                        'revert_reason_remarks' => $validated['remark'] ?? null,
                        'parent_id' => $parentId,
                    ]);
                }

                DB::commit();

                $this->dispatch('toastr', [
                    'type' => 'success',
                    'message' => 'Applications approved successfully!',
                ]);

                $this->clearSelected();
            } catch (\Throwable $e) {

                DB::rollBack();
                throw $e;
            }
        }
    }

    public function exportExcel(TableExportService $exportService)
    {
        return $exportService->export(
            $this,
            'applications_all.xlsx'
        );
    }
}
