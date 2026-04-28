<?php

namespace App\Livewire\CasteManagement;

use App\Helpers\FormOptionHelper;
use App\Models\CasteModificationInfo;
use App\Models\DynamicWorkflowLabel;
use App\Models\workflowstepRolemapping;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CasteModificationWorkflowTable extends DataTableComponent
{
    protected $model = CasteModificationInfo::class;

    public string $moduleCode;

    public int $schemeId;

    public int $schemeModuleId;

    public ?int $selectedStepId = null;

    public int $userRoleId = 0;

    public array $filterCondition = [];

    public function mount(string $moduleCode, int $schemeId, int $schemeModuleId, ?int $selectedStepId = null): void
    {
        $this->moduleCode = $moduleCode;
        $this->schemeId = $schemeId;
        $this->schemeModuleId = $schemeModuleId;
        $this->selectedStepId = $selectedStepId;

        $lgd = session('lgd_session');
        if (! empty($lgd['role_id'])) {
            try {
                $this->userRoleId = (int) Crypt::decryptString($lgd['role_id']);
            } catch (\Exception) {
            }
        }

        if (! empty($lgd['district_id'])) {
            try {
                $this->filterCondition['created_by_dist_code'] = Crypt::decryptString($lgd['district_id']);
            } catch (\Exception) {
            }
        }
        if (! empty($lgd['block_id'])) {
            try {
                $this->filterCondition['created_by_local_body_code'] = Crypt::decryptString($lgd['block_id']);
            } catch (\Exception) {
            }
        }
    }

    #[On('refreshDatatable')]
    public function refreshTable(): void
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

        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-indigo-800 text-xs uppercase py-3 px-4 text-white',
        ]);
        $this->setThAttributes(fn ($column) => ['class' => 'px-4 py-3 text-white bg-indigo-800 text-xs']);
        $this->setTdAttributes(fn ($row) => ['class' => 'px-4 py-3 text-gray-700 text-center']);
        $this->setTbodyAttributes(['class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto']);
    }

    public function columns(): array
    {
        return [
            Column::make('Ref No', 'id')
                ->format(fn ($value) => 'CM-'.$value),
            Column::make('Application ID', 'application_id'),
            Column::make('Name')
                ->label(fn ($row) => $row->beneficiaryPersonal?->beneficiary_name ?? 'N/A'),
            Column::make('Current Caste')
                ->label(function ($row) {
                    $oldData = is_string($row->old_data) ? json_decode($row->old_data, true) : $row->old_data;

                    return FormOptionHelper::label('Caste', $oldData['caste']);
                }),

            Column::make('Proposed Caste')
                ->label(function ($row) {
                    $newData = is_string($row->new_data) ? json_decode($row->new_data, true) : $row->new_data;

                    return FormOptionHelper::label(
                        'Caste',
                        isset($row->new_data['caste']) ? (int) $row->new_data['caste'] : null
                    );
                }),

            Column::make('Submitted At', 'created_at')
                ->format(fn ($value) => $value?->format('d M Y, h:i A') ?? '—'),
            Column::make('Status', 'current_step_id')
                ->format(function ($value, $row) {
                    if ($row->current_rank == -1) {
                        return '<span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">Rejected</span>';
                    }
                    if ($row->current_rank == -$this->schemeId) {
                        return '<span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-semibold text-orange-800">Reverted</span>';
                    }
                    $label = DynamicWorkflowLabel::find($value)?->label_name ?? 'Pending';

                    return '<span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">'.e($label).'</span>';
                })
                ->html(),
            Column::make('Actions')
                ->label(fn ($row) => view('coulmn_button.view', [
                    'link' => route('view-beneficiary-details', [
                        'application_id' => Crypt::encryptString($row->application_id),
                        'Scheme' => Crypt::encryptString($row->scheme_id),
                    ]),
                    'tooltip' => ($row->current_rank < 0) ? 'View Details' : 'Verify/Approve',
                ])->render())
                ->html()
                ->hideIf($this->selectedStepId != -$this->schemeId),
        ];
    }

    public function builder(): Builder
    {
        $assignedLabelIds = workflowstepRolemapping::where([
            'role_id' => $this->userRoleId,
            'module_id' => $this->schemeModuleId,
            'scheme_id' => $this->schemeId,
        ])
            ->distinct()
            ->pluck('workflow_step_id');
        $query = CasteModificationInfo::query()
            ->select([
                'id',
                'application_id',
                'beneficiary_id',
                'scheme_id',
                'old_data',
                'new_data',
                'caste_request_type',
                'request_id',
                'module_id',
                'current_step_id',
                'current_rank',
            ])
            ->with(['beneficiaryPersonal' => function ($query) {
                $query->select('application_id', 'beneficiary_name');
            }])
            ->where([
                'module_id' => $this->schemeModuleId,
                'scheme_id' => $this->schemeId,
            ])
            ->when($this->selectedStepId !== null, function ($query) {
                if ($this->selectedStepId < 0) {
                    $query->where('current_rank', $this->selectedStepId);
                } else {
                    $query->where('current_step_id', $this->selectedStepId);
                }
            }, function ($query) use ($assignedLabelIds) {
                $query->whereIn('current_step_id', $assignedLabelIds);
            })

            ->when(! empty($this->filterCondition), function ($query) {
                $query->whereHas('beneficiaryPersonal', function ($q) {
                    $q->where($this->filterCondition);
                });
            })

            ->latest(); // orderByDesc('created_at')

        // dd($query->get());
        return $query;
    }
}
