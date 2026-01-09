<?php

namespace App\Livewire;

use App\Models\Codemaster;
use App\Helpers\EncryptionArray;
use App\Models\BeneficiaryPersonal;
use App\Exports\BeneficiariesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Support\HtmlString;
use App\Models\FaultyBeneficiaryPersonal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Actions\Action;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class DraftBeneficiaryDetailsTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward;
    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public function mount(): void
    {
        $select_lgd = session('lgd_session');

        // foreach ($select_lgd as $key => $val) {
        //     $this->filter_condition[$key] = Crypt::decryptString($val);
        // }

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
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
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

        // ->setFilterLayoutSlideDown()
        // ->setFiltersEnabled();
        //  $this->setPaginationDropdownVisibilityDisabled();

        //      $this->setBulkActionsMenuItemAttributes([
        //     'class' => 'bg-blue-200 text-gray-700 px-3 py-1 rounded hover:bg-blue-300', // default style
        // ]);
        //  $this->setBulkActionsEnabled();
        // $this->setSelectAllDisabled();

        //      $this->setTableAttributes([
        // 'class' => 'min-w-full divide-y divide-gray-200 bg-white shadow-md rounded-lg p-4',
        // ]);

        //     $this->setBulkActionsTrCheckboxAttributes([
        // 'class' => 'hidden', // hide header checkbox if needed
        //     ]);

        // $this->setQueryStringForFilterEnabled();
        // $this->setQueryStringForSearchEnabled()
        //     ->setQueryStringForPerPageEnabled()
        //     ->setQueryStringForFiltersEnabled();
        //         $this->setPerPageDropdownAttributes([
        //     'class' => 'border rounded px-3 py-1 bg-white text-gray-700 hover:border-gray-500',
        // ]);

        // // Optional: wrapper for dropdown to control position
        // $this->setPerPageDropdownWrapperAttributes([
        //     'class' => 'mb-2 flex justify-end items-center', // e.g., place top-right
        // ]);
        // $this->setPerPageFieldAttributes([
        //     'class' => 'inline-flex justify-center px-4 py-2 text-sm font-medium rounded-md border shadow-sm focus:ring focus:ring-opacity-50
        //             text-gray-700 bg-white border-gray-300 hover:bg-gray-50 focus:border-indigo-300 focus:ring-indigo-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600',
        // ]);

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
    // public function bulkActions(): array
    // {
    //     return [
    //         'bulkapprove' => 'Approve',
    //         'exportSelected' => 'Export',
    //     ];
    // }
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

            TextFilter::make('Beneficiary ID')
                ->filter(function ($query, $value) {
                    $query->whereHas('sourceable', function ($q) use ($value) {
                        $q->where('beneficiary_id', 'ILIKE', "%{$value}%");
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
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->sourceable->application_id ?? 'N/A')
                ->sortable(),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) =>  $row->sourceable->full_name ?? 'N/A'),

            Column::make("Mobile No", "mobile_no")
                ->label(fn($row) => $row->mobile_no ?? 'N/A'),

            $columns[] = Column::make("Actions")
                ->label(function ($row) {
                    return view('coulmn_button.actions', [
                        'link' => route('draftedit'). '?app_id=' . Crypt::encryptString($row->sourceable->application_id),
                        'tooltip' => 'Edit Application',
                    ])->render();
                })
                ->html(),
        ];
    }
    public function builder(): Builder
    {
        $query = BeneficiaryCommonList::with('sourceable.relationships', 'sourceable.contact');
        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                // $this->district_id ? (int) $this->district_id : null,
                // $this->rural_urban ? (int) $this->rural_urban : null,
                // $this->blockurban ? (int) $this->blockurban : null,
                // $this->gp_ward ? (int) $this->gp_ward : null

                  $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null,
                $this->sub_div ? (int) $this->sub_div : null
            );
        }

        $next_level_role_id = Codemaster::getIdByCode(21);
        $query->whereHas(
            'sourceable',
            function ($q) use ($next_level_role_id) {
                $q->where('next_level_role_id', $next_level_role_id);
            }
        );
        // $query = DraftBeneficiaryPersonal::with('contact', 'bank')
        //     ->where('next_level_role_id',$next_level_role_id);
        // dd($query);
        //      ->whereIn('sourceable_type', [
        //     BeneficiaryPersonal::class,
        //     FaultyBeneficiaryPersonal::class
        // ]);
        // if (!empty($this->filter_condition['district_id'])) {
        //     $query->whereHas(
        //         'contact',
        //         fn($q) =>
        //         $q->where('district_id', $this->filter_condition['district_id'])
        //     );
        // }

        // if (!empty($this->filter_condition['block_id'])) {
        //     $query->whereHas(
        //         'contact',
        //         fn($q) =>
        //         $q->where('block_id', $this->filter_condition['block_id'])
        //     );
        // }

        // if (!empty($this->filter_condition['subdivision_id'])) {
        //     $query->whereHas(
        //         'contact.municipality',
        //         fn($mq) =>
        //         $mq->where('subdivision_id', $this->filter_condition['subdivision_id'])
        //     );
        // }
        // // $result = $query->get();
        // // dd($result);
        // if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
        //     $query = EncryptionArray::applyLocationFilters(
        //         $query,
        //         $this->district_id ? (int) $this->district_id : null,
        //         $this->rural_urban ? (int) $this->rural_urban : null,
        //         $this->blockurban ? (int) $this->blockurban : null,
        //         $this->gp_ward ? (int) $this->gp_ward : null
        //     );
        // }
        $this->dispatch('hideLoader');
        return $query;
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
