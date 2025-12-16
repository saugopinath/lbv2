<?php

namespace App\Livewire;

use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryCommonList;
use App\Models\Codemaster;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\BeneficiariesExport;
use App\Helpers\CheckAuthHelper;
use App\Models\BeneficiaryModificationAllowed;
use Illuminate\Support\Facades\Crypt;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class MarkedBeneficiaryDetailsListTable extends DataTableComponent

{
    public int $rowNumberOffset = 0;
    public array $filter_condition = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->rowNumberOffset = ($this->getPage() - 1) * $this->getPerPage();

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
        $this->setLoadingPlaceholderEnabled();
        $this->setConfigurableAreas([
            'toolbar-left-start' => 'livewire.export_excel_buttons',
        ]);
    }
    public function mount($applicantStatus = '', $casteId = '')
    {
        $select_lgd = session('lgd_session') ?? [];

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['sub_division_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
        // Show actions based on status and role
        // $this->showActions();
    }
    private function getStatusMessage($status): string
    {
        return match ($status) {
            'APL' => 'Verified but Pending for Approval',
            'VPL' => 'Pending for Verification',
            'AL'  => 'Application Already Approved',
            'RL'  => 'Application Already Reverted',
            'VL'  => 'Application Already Verified',
            default => 'No Action Required',
        };
    }

    public function setFilters($filters): void
    {
        // dd('bhbhbjhb');
        // $this->applicantStatus = $filters['status'] ?? '';
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->resetPage();
    }
    // select all value through orm 
    public function builder(): Builder
    {
        $query = BeneficiaryModificationAllowed::query()
            // ->select('beneficiary_modification_alloweds.*')
            ->select([
                'id',
                'application_id',
                'is_active',
                'allowed_fields',
            ])
            ->with([
                'beneficiaryCommonList',
            ])
            ->whereHas('beneficiaryCommonList', function ($q) {
                foreach ($this->filter_condition as $column => $value) {
                    $q->where($column, $value);
                }
                $q->where('sourceable_type', BeneficiaryPersonal::class);
            });
        //$query1= $query;
        // dd($query->get());
        // dd(['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        return $query;
    }



    public function columns(): array
    {
        return [
            Column::make('ID', 'id'),

            Column::make('Application Id', 'application_id')
                ->searchable(),

            Column::make('Name')
                ->label(
                    fn($row) =>
                    $row->beneficiaryCommonList?->sourceable?->full_name ?? 'N/A'
                    //  $row->beneficiaryCommonList?->beneficiary_name ?? 'N/A'
                )
                ->searchable(),
                Column::make('Mobile No')
                ->label(
                    fn($row) =>
                    $row->beneficiaryCommonList?->mobile_no ?? 'N/A'
                )
                ->searchable(),
            Column::make('Address')
                ->label(
                    fn($row) =>
                    $row->beneficiaryCommonList?->getFullAddress() ?? 'N/A'
                )
                ->html(),
            Column::make('Allowed Fields')
                ->label(function ($row) {
                    $fields = $row->getAllowedFieldNames();

                    if (empty($fields)) {
                        return '<div class="text-left py-3 px-4 border border-dashed border-gray-300 rounded-lg">
                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm text-gray-500 block">No fields allowed</span>
                </div>';
                    }

                    return '<div class="text-left">
                ' . collect($fields)->map(
                        fn($field) =>
                        '<div class="flex items-center px-3 py-1.5 bg-white border border-blue-200 rounded-lg shadow-sm hover:shadow transition-shadow mb-2 last:mb-0">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-blue-700">' . e($field) . '</span>
                    </div>'
                    )->implode('') . '
            </div>';
                })
                ->html(),


            Column::make('Actions')
                ->label(function ($row) {
                    // dd($row->is_active);
                    // dd($row->getAttributes());
                    if ((CheckAuthHelper::isCommonApprover()) && $row->is_active === true) {
                        return view('coulmn_button.view', [
                            'link' => route('view-marked-beneficiary-details', [
                                'application_id' => Crypt::encryptString($row->application_id),
                            ]),
                            'tooltip' => 'View Application',
                        ])->render();
                    } elseif (((CheckAuthHelper::isCommonHOD()) && $row->is_active === true)) {
                        return '<div class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-amber-700 bg-gradient-to-r from-amber-100 to-amber-50 border border-amber-200 shadow-sm">
                    <svg class="w-3.5 h-3.5 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Action Pending
                </div>';
                    } else {
                        return '<div class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-green-700 bg-gradient-to-r from-green-100 to-green-50 border border-green-200 shadow-sm">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Details Already Updated
                </div>';
                    }
                })
                ->html(),
        ];
    }
}
