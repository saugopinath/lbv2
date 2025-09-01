<?php

namespace App\Livewire;

use App\Models\SchemeValidationParameterSetting;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class MasterParameterDataTable extends DataTableComponent
{
    public ?int $perPage = 5;

    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled();


        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm text-center',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white text-center',
        ]);
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-4 py-3 text-white bg-violet-800 text-xs text-center',
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
            Column::make("Scheme Name")
                ->label(fn($row) => $row->scheme?->name ?? '-'),

            Column::make("Master Failed Type")
                ->label(fn($row) => $row->menu?->name ?? '-'),

            Column::make("Parameters")
                ->label(fn($row) => implode(', ', $row->parameter_names)),
            // Column::make("Min Score")
            //     ->label(fn($row) => $row->min_score ?? '-'),
            // Column::make("Max Score")
            //     ->label(fn($row) => $row->max_score ?? '-'),
            
        ];
    }

    public function builder(): Builder
    {
        $query = SchemeValidationParameterSetting::query()
            ->selectRaw('scheme_id, master_code, string_agg(parameter_code::text, \',\') as parameter_codes')
            ->groupBy('scheme_id', 'master_code')
            ->with(['scheme', 'menu']);

        // dd($query );
        return $query;
    }
}
