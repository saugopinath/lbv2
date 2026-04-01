<?php

namespace App\Livewire;

use App\Helpers\CheckAuthHelper;
use App\Helpers\EncryptionArray;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use App\Models\WorkflowsteproleMapping;
use App\Services\TableExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use App\Services\WorkflowService;
class BeneficiaryTable extends DataTableComponent
{
    public ?int $perPage = 5;

    public string $reportType;

    public string $search = '';

    public $district_id;

    public $rural_urban;

    public $blockurban;

    public $gp_ward;

    public $sub_div;

    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode;

    public $loginSubdivisionCode;

    public $loginBlockCode;

    public array $filter_condition = [];

    public $relationFather;

    public $schemeName;

    public $schemeId;

    public function mount(string $reportType = '', $schemeName = null, $schemeId = null): void
    {
        $this->schemeId = $schemeId;
        $this->reportType = $reportType;
        $this->relationFather = Codemaster::getIdByCode(131);

        // dd($this->relationFather);
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
        // dd($filters['gp_ward']);
        $this->district_id = $filters['district_id'];
        // dd($this->district_id );
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
        $this->sub_div = $filters['subdivision_id'];
        // dd($this->gp_ward );
    }

    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchdisabled()
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
        $columns = [
            Column::make('Application ID', 'application_id')
                ->label(fn ($row) => $row->application_id ?? 'N/A'),

            Column::make('Applicant Name', 'full_name')
                ->label(fn ($row) => $row->beneficiary_name ?? 'N/A'),

            Column::make("Father's Name")
                ->label(fn ($row) => $row->ben_father_name ?? 'N/A'),

            Column::make('Age', 'dob')
                ->label(fn ($row) => $row->dob ?? 'N/A'),
        ];

        if (in_array($this->reportType, ['1', '5', '4'])) {
            $columns[] = Column::make('Applicant Mobile No.', 'mobile_no')
                ->label(fn ($row) => $row->mobile_no ?? 'N/A');
        }

        if ($this->reportType == '3') {
            $beneficiaryColumn = Column::make('Beneficiary ID', 'beneficiary_id')
                ->label(fn ($row) => $row->beneficiary_id ?? 'N/A');

            array_unshift($columns, $beneficiaryColumn);
        }

        $columns[] = Column::make('Actions')
            ->label(function ($row) {
                if (($this->reportType == '3') || ($this->reportType == '2')) {
                    return view('coulmn_button.view', [
                        'link' => route('custom_application.view', [
                            'id' => Crypt::encryptString($row->application_id),
                            'scheme_id' => Crypt::encryptString($row->scheme_id),
                        ]),
                        'tooltip' => 'View Application',
                    ])->render();
                } elseif ((($this->reportType == '1') || ($this->reportType == '6') || ($this->reportType == '5')) && (CheckAuthHelper::isCommonOperator())) {
                    return view('coulmn_button.actions', [
                        'link' => route('draftedit').'?app_id='.Crypt::encryptString($row->application_id).'&ben_id='.Crypt::encryptString($row->beneficiary_id).'&scheme_id='.Crypt::encryptString($row->scheme_id),
                        'tooltip' => 'Edit Application',
                    ])->render();
                } else {
                    return 'N/A';
                }
            })
            ->html();

        if ($this->reportType === '2') {
            return $columns;
        }

        return $columns;
    }

    public function builder(): Builder
    {
        $workflowService = new WorkflowService();
        $minMaxWorkflowStep = WorkflowsteproleMapping::getMinMaxWorkflowStep($this->schemeId);
        $maxData = $workflowService->getLabelRoles($this->schemeId,$minMaxWorkflowStep['max']);
        $minData = $workflowService->getLabelRoles($this->schemeId,$minMaxWorkflowStep['min']);
        // Status Constants
        $STATUS_VERIFIED = $maxData->same_label_role_id;
        $STATUS_APPROVED = $maxData->next_label_role_id;
        $STATUS_FINAL = $minData->next_label_role_id;
        $STATUS_REJECT = -100;
        $STATUS_REVERT = $minData->same_label_role_id;
        $STATUS_PARTIAL = null;

        $nextLevelRoleId = null;

        // Default condition
        $extraConditions = [
            'scheme_id' => $this->schemeId,
        ];

        switch ($this->reportType) {

            case '1': // Partial
                $extraConditions['is_final'] = 0;
                $nextLevelRoleId = $STATUS_PARTIAL;
                break;

            case '2': // Verified
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_VERIFIED;
                break;

            case '3': // Approved
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_APPROVED;
                break;

            case '4': // Rejected
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_REJECT;
                break;

            case '5': // Reverted
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_REVERT;
                break;

            case '6': // Final
                $extraConditions['is_final'] = 1;
                $nextLevelRoleId = $STATUS_FINAL;
                break;
        }

        /**
         * --- BASE QUERY ---
         */
        $query = BeneficiaryPersonalDetail::with('contact')
            ->where(function ($q) use ($extraConditions, $nextLevelRoleId) {

                foreach ($extraConditions as $field => $value) {
                    $q->where($field, $value);
                }

                $q->where('next_level_role_id', $nextLevelRoleId);
            });

        /**
         * --- EXTRA FILTER CONDITION ---
         */
        if (! empty($this->filter_condition)) {
            $query->where($this->filter_condition);
        }

        /**
         * --- LOCATION FILTER ---
         */
        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->district_id ?: null,
                $this->rural_urban ?: null,
                $this->blockurban ?: null,
                $this->gp_ward ?: null,
                $this->sub_div ?: null
            );
        }

        $this->dispatch('hideLoader');

        return $query;
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

    public function exportExcel(TableExportService $exportService)
    {
        return $exportService->export(
            $this,
            'applications_all.xlsx'
        );
    }
}
