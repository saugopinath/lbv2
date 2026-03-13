<?php

namespace App\Livewire;

use App\Models\WorkflowStep;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Livewire\Attributes\On;

class assignworkflowDatatable extends DataTableComponent
{
    public ?int $perPage = 5;
    public $schemeId = null;
    #[On('scheme-changed')]
    public function setScheme($schemeId)
    {
        $this->schemeId = $schemeId;
    }
    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
    }
    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchDisabled()
            ->setSearchLive()
            ->setColumnSelectDisabled()
        ;
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
            Column::make('Id')
                ->label(fn($row) => $row->id),
            Column::make('Rank')
                ->label(fn($row) => $row->rank),
            Column::make('Label Name')
                ->label(fn($row) => $row->label),
            Column::make('Action')
                ->label(function ($row) {
                    return view('coulmn_button.actions', [
                        'wireClick' => "\$dispatch('openassignworkflowModal', { id: '$row->id' })",
                        'tooltip' => 'Assign Workflow',
                    ])->render();
                })
                ->html(),
        ];
    }
    
    public function builder(): Builder
    {
        return WorkflowStep::query()
            ->with('scheme')
            ->when(
                $this->schemeId,
                fn($q) =>
                $q->where('scheme_id', $this->schemeId)
            );
    }
}
