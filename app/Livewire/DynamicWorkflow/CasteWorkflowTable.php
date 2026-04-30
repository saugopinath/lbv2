<?php

namespace App\Livewire\DynamicWorkflow;

use App\Models\CasteModificationInfo;
use App\Models\DynamicWorkflowModule;
use App\Models\DynamicWorkflowRequest;
use App\Models\DynamicWorkflowSchemeModule;
use App\Models\UserRoleSchemeOfficeMapping;
use App\Models\workflowstepRolemapping;
use App\Helpers\FormOptionHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CasteWorkflowTable extends DataTableComponent
{
    protected $model = CasteModificationInfo::class;

    public $moduleCode;

    public $scheme_id;

    public $module_id;

    public $filter_condition = [];

    public $RoleId;

    public function mount($moduleCode, $schemeId)
    {
        $this->moduleCode = $moduleCode;
        $this->scheme_id = (int) $schemeId;
        $Mainmodule = DynamicWorkflowModule::where('module_code', $this->moduleCode)->first();
        if (! $Mainmodule) {
            abort(404, 'Module not found');
        }
        $module = DynamicWorkflowSchemeModule::where('module_id', $Mainmodule->id)->where('scheme_id', $this->scheme_id)->first();
        // dd($module);
        $this->module_id = $module->id;
        if (! $module) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Steps are not configured for this scheme!',
            ]);

            return;
        }
        $selectLgd = session('lgd_session');
        if (! empty($selectLgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($selectLgd['district_id']);
        }
        if (! empty($selectLgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($selectLgd['block_id']);
        }
        if (! empty($selectLgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($selectLgd['subdivision_id']);
        }
        if (! empty($selectLgd['role_id'])) {
            $this->RoleId = Crypt::decryptString($selectLgd['role_id']);
        }
    }

    #[On('refreshDatatable')]
    public function refreshTable()
    {
        $this->dispatch('$refresh');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([10, 20, 50])
            ->setPerPage(10)
            ->setSearchEnabled()
            ->setSearchLive();

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

    public function columns(): array
    {
        return [
            Column::make('Reference ID', 'id')
                ->format(fn ($value) => 'REF NO-'.$value),
            Column::make('Application ID', 'application_id'),
            Column::make('Name')
                ->label(
                    fn ($row) => $row->beneficiaryPersonal?->beneficiary_name ?? 'N/A'
                ),
            Column::make('Old Caste', 'old_data')
                ->format(fn ($value) => FormOptionHelper::label('Caste', $value['caste'] ?? null)),
            Column::make('New Caste', 'new_data')
                ->format(fn ($value) => FormOptionHelper::label('Caste', $value['caste'] ?? null)),

            Column::make('Created At', 'created_at'),
            Column::make('Action')
                ->label(function ($row) {
                    return '<button wire:click="$dispatch(\'openCasteWorkflowModal\', { requestId: '.$row->id.', scheme_id: '.$this->scheme_id.', module_id: '.$this->module_id.' })" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all transform active:scale-95 shadow-md font-bold text-xs uppercase">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>';
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        $userRoleId = $this->RoleId;

        if (! $userRoleId) {
            $userRoleId = (int) UserRoleSchemeOfficeMapping::where('user_id', Auth::id())
                ->where('is_active', 1)
                ->value('role_id') ?? 0;
        }
        $Mainmodule = DynamicWorkflowModule::where('module_code', $this->moduleCode)->first();
        if (! $Mainmodule) {
            abort(404, 'Module not found');
        }
        $module = DynamicWorkflowSchemeModule::where('module_id', $Mainmodule->id)->where('scheme_id', $this->scheme_id)->first();
        if (! $module) {
            return CasteModificationInfo::query()->whereRaw('1=0');
        }
        $userRanks = workflowstepRolemapping::where('role_id', $userRoleId)
            ->where('module_id', $module->id)
            ->where('scheme_id', $this->scheme_id)
            ->pluck('rank')
            ->toArray();
        // dd($userRanks);
        /** @var Builder $result */
        $result = CasteModificationInfo::query()
            ->with(['module', 'step.label', 'step.role'])
            ->where('module_id', $module->id)
            ->whereIn('current_rank', $userRanks)
            ->where('scheme_id', $this->scheme_id);
        // dd($result->get());
        if (! empty($this->filter_condition['created_by_dist_code'])) {
            $result->whereHas('beneficiaryPersonal', function ($q) {
                $q->where('created_by_dist_code', $this->filter_condition['created_by_dist_code']);
            });
        }
        if (! empty($this->filter_condition['created_by_local_body_code'])) {
            $result->whereHas('beneficiaryPersonal', function ($q) {
                $q->where('created_by_local_body_code', $this->filter_condition['created_by_local_body_code']);
            });
        }

        return $result->orderBy('created_at', 'desc');
    }
}
