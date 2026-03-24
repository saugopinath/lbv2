<?php

namespace App\Livewire\DynamicWorkflow;

use App\Models\DynamicWorkflowRequest;
use App\Models\workflowstepRolemapping;
use App\Models\DynamicWorkflowModule;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;

class RequestWorkflowTable extends DataTableComponent
{
    protected $model = DynamicWorkflowRequest::class;
    public $moduleCode;
    public $scheme_id;
    public $module_id;

    public function mount($moduleCode, $schemeId)
    {
        $this->moduleCode = $moduleCode;
        $this->scheme_id = (int) $schemeId;
        $this->module_id = DynamicWorkflowModule::where('module_code', $this->moduleCode)
            ->where('scheme_id', $this->scheme_id)
            ->value('id');
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
            Column::make("Reference ID", "id")
                ->format(fn($value) => 'REF NO-' . $value),
            Column::make("Application ID", "ref_id"),
            Column::make("Changed Fields", "changed_fields")
                ->format(function ($value) {
                    if (empty($value) || !is_array($value)) {
                        return '-';
                    }
                    $labels = [
                        'beneficiary_name' => 'Name Update',
                        'dob_age'          => 'DOB / Age Update',
                        'mobile_no'        => 'Mobile Update',
                        'bank_details'     => 'Bank Details Update',
                    ];
                    $formatted = [];
                    foreach ($value as $index => $slug) {
                        $displayLabel = $labels[$slug] ?? ucfirst(str_replace('_', ' ', $slug));
                        $formatted[] = '<span class="font-medium">' . ($index + 1) . '. ' . $displayLabel . '</span>';
                    }

                    return implode('<br>', $formatted);
                })
                ->html(),
            Column::make("Created At", "created_at"),
            Column::make("Action")
                ->label(function ($row) {
                    return '<button wire:click="$dispatch(\'openProcessModal\', { requestId: ' . $row->id . ', scheme_id: ' . $this->scheme_id . ', module_id: ' . $this->module_id . ' })" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all transform active:scale-95 shadow-md font-bold text-xs uppercase">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>';
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        $lgd_session = session('lgd_session');
        $userRoleId = 0;

        if (!empty($lgd_session['role_id'])) {
            try {
                $userRoleId = (int) Crypt::decryptString($lgd_session['role_id']);
            } catch (\Exception $e) {
            }
        }
        if (!$userRoleId) {
            $userRoleId = (int) \App\Models\UserRoleSchemeOfficeMapping::where('user_id', Auth::id())
                ->where('is_active', 1)
                ->value('role_id') ?? 0;
        }
        $module = DynamicWorkflowModule::where('module_code', $this->moduleCode)->where('scheme_id', $this->scheme_id)->first();
        if (!$module) {
            return DynamicWorkflowRequest::query()->whereRaw('1=0');
        }
        $userRanks = workflowstepRolemapping::where('role_id', $userRoleId)
            ->where('module_id', $module->id)
            ->where('scheme_id', $this->scheme_id)
            ->pluck('rank')
            ->toArray();
        $result = DynamicWorkflowRequest::query()
            ->with(['module', 'step.label', 'step.role'])
            ->where('module_id', $module->id)
            ->whereIn('current_rank', $userRanks)
            ->where('scheme_id', $this->scheme_id)
            ->orderBy('created_at', 'desc');
        // dd($result->toSql(), $result->getBindings());
        return $result;
    }
}
