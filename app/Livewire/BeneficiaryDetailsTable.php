<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Codemaster;
use App\Models\BenRejectDetail;
use App\Models\BeneficiaryPersonal;
use App\Exports\BeneficiariesExport;
use App\Models\BeneficiaryContact;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
use App\Helpers\EncryptionArray;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class BeneficiaryDetailsTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward;
    protected $listeners = ['filtersApplied'];
    public $loginDistrictCode;
    public $loginSubdivisionCode;
    public $loginBlockCode;
    public  $filter_condition = [];
    public $where_condition = [];

    public function mount(string $reportType = '', string $login_type = ''): void
    {
        $this->reportType = $reportType;
        $this->login_type = $login_type;
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
        $this->setPrimaryKey($this->reportType === 'approved' ? 'beneficiary_id' : 'application_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive();
    }
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
    public function columns(): array
    {
        $columns = [
            Column::make("Application ID", "application_id")->searchable()->sortable(),
            Column::make("Applicant Name", "full_name")->searchable(),
            Column::make("Father's Name")
                ->label(fn($row) => optional($row->father->first())->full_name ?? 'N/A'),
            Column::make("Age", "dob")
                ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
        ];

        if (in_array($this->reportType, ['1', '5', '4'])) {
            $columns[] = Column::make("Applicant Mobile No.", "mobile_no");
        }

        if ($this->reportType === '3') {
            array_unshift($columns, Column::make("Beneficiary ID", "beneficiary_id"));
        }

        if ($this->reportType === '4') {
            $columns[2] = Column::make("Father's Name", "father_full_name");
            $columns[] = Column::make("Rejected Reason", "rejected_reason");
        }

        if ($this->reportType === '2') {
            return $columns;
        }

        return $columns;
    }
    public function builder(): Builder
    {
        $roleVerified = Codemaster::getIdByCode(22);
        $roleApproved = Codemaster::getIdByCode(23);
        $roleReverted = Codemaster::getIdByCode(21);
        $relationFather = Codemaster::getIdByCode(131);

        if ($this->reportType === "2") {
            $model = DraftBeneficiaryPersonal::with('contact');
            $next_level_role_id = $roleVerified;
        } elseif ($this->reportType === "3") {
            $model = BeneficiaryPersonal::with('contact');
            $next_level_role_id = $roleApproved;
        } elseif (in_array($this->reportType, ["1", "5"])) {
            $model = DraftBeneficiaryPersonal::with('contact');
            $next_level_role_id = $roleReverted;
        } elseif ($this->reportType === "4") {
            $query = BenRejectDetail::query();

            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->reportType,
                $this->district_id,
                $this->rural_urban,
                $this->blockurban,
                $this->gp_ward
            );
            return $query;
        }

        $query = $model->where('next_level_role_id', $next_level_role_id)
            ->with(['father' => fn($q) => $q->where('relation_type_id', $relationFather)])
            ->whereHas('father', fn($q) => $q->where('relation_type_id', $relationFather));


        $select_lgd = EncryptionArray::lgdsession();

        if ($select_lgd) {
            $select_lgd = array_map(function ($value) {
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    return $value;
                }
            }, $select_lgd);
        }

        if (!empty($select_lgd['district_id'])) {
            $filter_condition['district_id'] = $select_lgd['district_id'];
        }
        if (!empty($select_lgd['block_id'])) {
            $filter_condition['block_id'] = $select_lgd['block_id'];
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $query->whereHas(
                'contact.municipality',
                fn($mq) =>
                $mq->where('subdivision_id', $select_lgd['subdivision_id'])
            );
        }

        // if (!empty($select_lgd['subdivision_id'])) {
        //     $filter_condition['subdivision_id'] = $select_lgd['subdivision_id'];
        // }

        // if (!empty($filter_condition['subdivision_id'])) {
        //     $query->whereHas('contact', function ($q) use ($filter_condition) {
        //         $q->whereHas('municipality', function ($mq) use ($filter_condition) {
        //             $mq->where('subdivision_id', $filter_condition['subdivision_id']);
        //         });
        //     });
        // }

        $query = EncryptionArray::applyLocationFilters(
            $query,
            $this->reportType,
            $this->district_id,
            $this->rural_urban,
            $this->blockurban,
            $this->gp_ward
        );

        return $query;
    }
    public function export()
    {
        $reportTypeFormatted = ucfirst($this->reportType);
        $timestamp = Carbon::now('Asia/Kolkata')->format('Ymd_Hi');
        $filename = "{$reportTypeFormatted}_Beneficiaries_{$timestamp}.xlsx";

        return Excel::download(
            new BeneficiariesExport(
                $this->reportType,
                $this->login_type,
                $this->loginDistrictCode,
                $this->loginSubdivisionCode,
                $this->loginBlockCode
            ),
            $filename
        );
    }
    public function render(): \Illuminate\View\View
    {
        return view('livewire.custom-beneficiary-table', [
            'rows' => $this->getRows(),
            'reportType' => $this->reportType,
        ]);
    }
}
