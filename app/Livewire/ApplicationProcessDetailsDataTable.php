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
    public function mount($schemeId = null, WorkflowService $workflowService): void
    {
        $this->schemeId = $schemeId;
        $labelRoles = $workflowService->getLabelRoles($schemeId);
        if ($labelRoles) {
            $this->sameLabelRoleId = $labelRoles->same_label_role_id;
            $this->nextLabelRoleId = $labelRoles->next_label_role_id;
        }

        $select_lgd = session('lgd_session');

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['sub_division_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
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
        $this->setPrimaryKey('sourceable_id')
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
    // public function filters(): array
    // {
    //     return [
    //         TextFilter::make('Application ID')
    //             ->filter(function ($query, $value) {
    //                 $query->whereHas('sourceable', function ($q) use ($value) {
    //                     $q->where('application_id', 'ILIKE', "%{$value}%");
    //                 });
    //             }),

    //         TextFilter::make('Applicant Name')
    //             ->filter(function ($query, $value) {
    //                 $query->whereHas('sourceable', function ($q) use ($value) {
    //                     $q->where('full_name', 'ILIKE', "%{$value}%");
    //                 });
    //             }),
    //     ];
    // }

    // public function columns(): array
    // {
    //     return [
    //         Column::make("Application ID", "application_id")
    //             ->label(fn($row) => $row->sourceable->application_id ?? 'N/A')
    //             ->sortable()
    //             ->searchable(function ($query, $searchTerm) {
    //                 $query->whereHas('sourceable', function ($q) use ($searchTerm) {
    //                     $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
    //                 });
    //             }),

    //         Column::make("Applicant Name", "full_name")
    //             ->label(fn($row) => $row->sourceable->full_name ?? 'N/A'),

    //         Column::make("Father's Name", "fullname")
    //             ->label(function ($row) {
    //                 return optional(
    //                     $row->sourceable->relationships->firstWhere(
    //                         'relation_type_id',
    //                         Codemaster::getIdByCode(131)
    //                     )
    //                 )->full_name ?? 'N/A';
    //             }),

    //         Column::make("Age", "age")
    //             ->label(fn($row) => Carbon::parse($row->sourceable->dob)->age
    //                 ?? 'N/A'),

    //         $columns[] = Column::make("Actions")
    //             ->label(function ($row) {
    //                 return view('coulmn_button.view', [
    //                     'link' => route('draft-application.view', Crypt::encryptString($row->sourceable->application_id)),
    //                     'tooltip' => 'View Application',
    //                 ])->render();
    //             })
    //             ->html(),
    //     ];
    // }


    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->application_id ?? 'N/A'),

            Column::make("Applicant Name")
                ->label(fn($row) => $row->full_name ?? 'N/A'),

            Column::make("Father's Name")
                ->label(fn($row) => $row->ffname ?? 'N/A'),

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
        // $query = BeneficiaryCommonList::with('sourceable.relationships', 'sourceable.contact');
        // if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->sub_div) {
        //     $query = EncryptionArray::applyLocationFilters(
        //         $query,
        //         $this->district_id ? (int) $this->district_id : null,
        //         $this->rural_urban ? (int) $this->rural_urban : null,
        //         $this->blockurban ? (int) $this->blockurban : null,
        //         $this->gp_ward ? (int) $this->gp_ward : null,
        //         $this->sub_div ? (int) $this->sub_div : null
        //     );
        // }
        // // $user = auth()->user();
        // $next_level_role_id = null;

        // if (CheckAuthHelper::isCommonApprover()) {
        //     // if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
        //     $next_level_role_id = Codemaster::getIdByCode(23);
        // }
        // if (CheckAuthHelper::isCommmonVerifier()) {
        //     // if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
        //     $next_level_role_id = Codemaster::getIdByCode(22);
        // }
        // if (CheckAuthHelper::isOperator()) {
        //     // if ($user->hasRole('Operator')) {
        //     $next_level_role_id = Codemaster::getIdByCode(21);
        // }

        // $sourceableClass = DraftBeneficiaryPersonal::class;

        // if ($next_level_role_id) {
        //     $query->whereHasMorph(
        //         'sourceable',
        //         $sourceableClass,
        //         function ($q) use ($next_level_role_id) {
        //             $q->where('next_level_role_id', $next_level_role_id);
        //         }
        //     );
        // }

        // if (!empty($this->filter_condition)) {
        //     $query->where($this->filter_condition);
        // }
        // $this->dispatch('hideLoader');

        $query = BeneficiaryPersonalDetail::where('next_level_role_id', $this->sameLabelRoleId)->where('scheme_id', $this->schemeId);
        return $query;
    }

    public function bulkverify()
    {
        $ids = $this->getSelected();
        $approverRoleId = Codemaster::getIdByCode(23);
        $select_lgd = session('lgd_session');
        $user_id = Crypt::decryptString($select_lgd['role_id']);
        foreach ($ids as $id) {
            DB::beginTransaction();
            try {
                $DraftBeneficiaryPersonal = DraftBeneficiaryPersonal::find($id);
                $DraftBeneficiaryPersonal->next_level_role_id = $approverRoleId;
                $DraftBeneficiaryPersonal->save();
                $AcceptRejectInfo = new AcceptRejectInfo;
                $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                $AcceptRejectInfo->ip_address = request()->ip();
                $AcceptRejectInfo->user_id = Auth::id();
                $AcceptRejectInfo->browser = request()->header('User-Agent');
                $AcceptRejectInfo->model_name = null;
                $AcceptRejectInfo->op_type = $approverRoleId;
                $AcceptRejectInfo->revert_reason_cause_id = null;
                $AcceptRejectInfo->revert_reason_remarks = null;
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
            'message' => 'All applications verified successfully!'
        ]);
        $this->clearSelected();
    }


    public function bulkapprove()
    {

        $ids = $this->getSelected();
        // $drafts = DraftBeneficiaryPersonal::whereIn('application_id', $ids)->get();
        // foreach ($drafts as $draft) {
        //     $draft->delete();
        // }

        foreach ($ids as $id) {
            DB::beginTransaction();
            try {
                $DraftBeneficiaryPersonal = DraftBeneficiaryPersonal::find($id);
                $DraftBeneficiaryPersonal->next_level_role_id = Codemaster::getIdByCode(0);
                $DraftBeneficiaryPersonal->save();
                $AcceptRejectInfo = new AcceptRejectInfo;
                $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                $AcceptRejectInfo->ip_address = request()->ip();
                $AcceptRejectInfo->user_id = Auth::id();
                $AcceptRejectInfo->browser = request()->header('User-Agent');
                $AcceptRejectInfo->model_name = null;
                $AcceptRejectInfo->op_type = Codemaster::getIdByCode(0);
                $AcceptRejectInfo->revert_reason_cause_id = null;
                $AcceptRejectInfo->revert_reason_remarks = null;
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
    public function confirmBulkRevert($validated)
    {
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
                    $DraftBeneficiaryPersonal = DraftBeneficiaryPersonal::find($id);
                    $DraftBeneficiaryPersonal->next_level_role_id = $next_level_role_id;
                    $DraftBeneficiaryPersonal->save();
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                    $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = $next_level_role_id;
                    $AcceptRejectInfo->revert_reason_cause_id = $validated['reason'];
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
        } elseif ($this->revertrejectAction === 'reject') {
            $ids = $this->getSelected();
            foreach ($ids as $id) {
                DB::beginTransaction();
                try {
                    $DraftBeneficiaryPersonal = DraftBeneficiaryPersonal::find($id);
                    $DraftBeneficiaryPersonal->next_level_role_id = Codemaster::getIdByCode(-1);
                    $DraftBeneficiaryPersonal->save();
                    $AcceptRejectInfo = new AcceptRejectInfo;
                    $AcceptRejectInfo->application_id = $DraftBeneficiaryPersonal->application_id;
                    $AcceptRejectInfo->beneficiary_id = $DraftBeneficiaryPersonal->beneficiary_id;
                    $AcceptRejectInfo->ip_address = request()->ip();
                    $AcceptRejectInfo->user_id = Auth::id();
                    $AcceptRejectInfo->browser = request()->header('User-Agent');
                    $AcceptRejectInfo->model_name = null;
                    $AcceptRejectInfo->op_type = Codemaster::getIdByCode(-1);
                    $AcceptRejectInfo->revert_reason_cause_id = null;
                    $AcceptRejectInfo->revert_reason_remarks = null;
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
