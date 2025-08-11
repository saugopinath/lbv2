<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\BenRejectDetail;
use App\Models\BeneficiaryPersonal;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BeneficiariesExport;

class BeneficiaryDetailsTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType = '';
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward;

    protected $listeners = ['filtersApplied'];
    public $loginDistrictCode;
    public $loginSubdivisionCode;
    public $loginBlockCode;

    public function filtersApplied($filters)
    {
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
    }

    public function mount(string $reportType = '', string $login_type = ''): void
    {
        $this->reportType = $reportType;
        $this->login_type = $login_type;

        // $this->loginType = 'district_office';
        // $this->loginDistrictCode = 305;

        // $this->loginType = 'subdivision_office';
        // $this->loginSubdivisionCode = 250208;

        // $this->loginType = 'block_office';
        // $this->loginBlockCode = 2793;

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
        if ($this->reportType === 'verified') {
            return [
                Column::make("Application ID", "application_id")->searchable()->sortable(),
                Column::make("Applicant Name", "full_name")->searchable(),
                Column::make("Father's Name")
                    ->label(fn($row) => optional($row->father->first())->full_name ?? 'N/A'),
                Column::make("Age", "dob")
                    ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
            ];
        }

        if ($this->reportType === 'approved') {
            return [
                Column::make("Beneficiary ID", "beneficiary_id"),
                Column::make("Application ID", "application_id")->searchable()->sortable(),
                Column::make("Applicant Name", "full_name")->searchable(),
                Column::make("Father's Name")
                    ->label(fn($row) => optional($row->father->first())->full_name ?? 'N/A'),
                Column::make("Age", "dob")
                    ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
            ];
        }

        if ($this->reportType === 'reverted') {
            return [
                Column::make("Application ID", "application_id")->searchable()->sortable(),
                Column::make("Applicant Name", "full_name")->searchable(),
                Column::make("Father's Name")
                    ->label(fn($row) => optional($row->father->first())->full_name ?? 'N/A'),
                Column::make("Age", "dob")
                    ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
                Column::make("Applicant Mobile No.", "mobile_no"),
            ];
        }

        if ($this->reportType === 'partial') {
            return [
                Column::make("Application ID", "application_id")->searchable()->sortable(),
                Column::make("Applicant Name", "full_name")->searchable(),
                Column::make("Father's Name")
                    ->label(fn($row) => optional($row->father->first())->full_name ?? 'N/A'),
                Column::make("Age", "dob")
                    ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
                Column::make("Applicant Mobile No.", "mobile_no"),
            ];
        }

        if ($this->reportType === 'rejected') {
            return [
                Column::make("Application ID", "application_id")->searchable()->sortable(),
                Column::make("Applicant Name", "full_name")->searchable(),
                Column::make("Father's Name", "father_full_name"),
                Column::make("Age", "dob")
                    ->format(fn($value) => $value ? Carbon::parse($value)->age : 'N/A'),
                Column::make("Applicant Mobile No.", "mobile_no"),
                Column::make("Rejected Reason", "rejected_reason"),
            ];
        }

        return [];
    }

    public function builder(): Builder
    {
        if ($this->reportType === "verified") {
            $query = DraftBeneficiaryPersonal::query()
                ->where("next_level_role_id", 22)
                ->with(['father' => fn($q) => $q->where('relation_type_id', 79)])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', 79));
        }
        if ($this->reportType === "approved") {
            $query = BeneficiaryPersonal::query()
                ->where("next_level_role_id", 23)
                ->with(['father' => fn($q) => $q->where('relation_type_id', 79)])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', 79));
        }
        if ($this->reportType === "reverted" || $this->reportType === "partial") {
            $query = DraftBeneficiaryPersonal::query()
                ->where("next_level_role_id", 21)
                ->with(['father' => fn($q) => $q->where('relation_type_id', 79)])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', 79));
        }
        if ($this->reportType === "rejected") {
            $query = BenRejectDetail::query();
        }

        if (!$query) {
            return DraftBeneficiaryPersonal::query()->whereRaw("1=0");
        }


        if ($this->login_type === 'district_office' && $this->loginDistrictCode) {
            $query->where('district_id', $this->loginDistrictCode);
        } elseif ($this->login_type === 'subdivision_office' && $this->loginSubdivisionCode) {
            $query->where('municipality_id', $this->loginSubdivisionCode);
        } elseif ($this->login_type === 'block_office' && $this->loginBlockCode) {
            $query->where('block_id', $this->loginBlockCode);
        }

        if ($this->reportType !== "rejected") {
            $query->with('contact');

            if ($this->district_id) {
                $query->whereHas('contact', fn($q) => $q->where('district_id', $this->district_id));
            }
            if ($this->blockurban && $this->rural_urban) {
                $query->whereHas('contact', function ($q) {
                    if ($this->rural_urban == 2) {
                        $q->where('block_id', $this->blockurban);
                    } elseif ($this->rural_urban == 1) {
                        $q->where('municipality_id', $this->blockurban);
                    }
                });
            }
            if ($this->gp_ward && $this->rural_urban) {
                $query->whereHas('contact', function ($q) {
                    if ($this->rural_urban == 2) {
                        $q->where('panchayat_id', $this->gp_ward);
                    } elseif ($this->rural_urban == 1) {
                        $q->where('ward_id', $this->gp_ward);
                    }
                });
            }
        } else {
            if ($this->district_id) {
                $query->where('district_id', $this->district_id);
            }
            if ($this->blockurban && $this->rural_urban) {
                if ($this->rural_urban == 2) {
                    $query->where('block_id', $this->blockurban);
                } elseif ($this->rural_urban == 1) {
                    $query->where('municipality_id', $this->blockurban);
                }
            }
            if ($this->gp_ward && $this->rural_urban) {
                if ($this->rural_urban == 2) {
                    $query->where('panchayat_id', $this->gp_ward);
                } elseif ($this->rural_urban == 1) {
                    $query->where('ward_id', $this->gp_ward);
                }
            }
        }

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
