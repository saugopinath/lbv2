<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Codemaster;
use App\Models\BenRejectDetail;
use App\Models\BeneficiaryPersonal;
use App\Models\DraftBeneficiaryPersonal;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class BeneficiariesExport implements FromCollection, WithHeadings
{
    protected $reportType;
    protected $loginType;
    protected $districtCode;
    protected $subdivisionCode;
    protected $blockCode;

    public function __construct($reportType, $loginType, $districtCode, $subdivisionCode, $blockCode)
    {
        $this->reportType = $reportType;
        $this->loginType = $loginType;
        $this->districtCode = $districtCode;
        $this->subdivisionCode = $subdivisionCode;
        $this->blockCode = $blockCode;
    }

    public function collection()
    {
        $roleVerified = Codemaster::getIdByCode(22);
        $roleApproved = Codemaster::getIdByCode(23);
        $roleReverted = Codemaster::getIdByCode(21);
        $relationFather = Codemaster::getIdByCode(131);
        // dd($roleReverted);
        if ($this->reportType === "2") {
            $query = DraftBeneficiaryPersonal::query()
                ->where("next_level_role_id", $roleVerified)
                ->with(['father' => fn($q) => $q->where('relation_type_id', $relationFather)])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', $relationFather));
        }
        if ($this->reportType === "3") {
            $query = BeneficiaryPersonal::query()
                ->where("next_level_role_id", $roleApproved)
                ->with(['father' => fn($q) => $q->where('relation_type_id', $relationFather)])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', $relationFather));
        }
        if ($this->reportType === "1" || $this->reportType === "5") {
            $query = DraftBeneficiaryPersonal::query()
                ->where("next_level_role_id", $roleReverted)
                ->with(['father' => fn($q) => $q->where('relation_type_id', $relationFather)])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', $relationFather));
        }
        if ($this->reportType === "4") {
            $query = BenRejectDetail::query();
        }

        if ($this->loginType === '152' && $this->districtCode) {
            $query->where('district_id', $this->districtCode);
        } elseif ($this->loginType === '154' && $this->subdivisionCode) {
            $query->where('municipality_id', $this->subdivisionCode);
        } elseif ($this->loginType === '153' && $this->blockCode) {
            $query->where('block_id', $this->blockCode);
        }

        $data = $query->get();

        return $data->map(function ($item) {
            if ($this->reportType === '2') {
                return [
                    'Application ID' => $item->application_id,
                    'Applicant Name' => $item->full_name,
                    'Father\'s Name' => optional($item->father->first())->full_name ?? 'N/A',
                    'Age' => $item->dob ? Carbon::parse($item->dob)->age : 'N/A',
                ];
            }

            if ($this->reportType === '3') {
                return [
                    'Beneficiary ID' => $item->beneficiary_id,
                    'Application ID' => $item->application_id,
                    'Applicant Name' => $item->full_name,
                    'Father\'s Name' => optional($item->father->first())->full_name ?? 'N/A',
                    'Age' => $item->dob ? Carbon::parse($item->dob)->age : 'N/A',
                ];
            }

            if (in_array($this->reportType, ['1', '5'])) {
                return [
                    'Application ID' => $item->application_id,
                    'Applicant Name' => $item->full_name,
                    'Father\'s Name' => optional($item->father->first())->full_name ?? 'N/A',
                    'Age' => $item->dob ? Carbon::parse($item->dob)->age : 'N/A',
                    'Mobile No' => $item->mobile_no,
                ];
            }

            if ($this->reportType === '4') {
                return [
                    'Application ID' => $item->application_id,
                    'Applicant Name' => $item->full_name,
                    'Father\'s Name' => $item->father_full_name,
                    'Age' => $item->dob ? Carbon::parse($item->dob)->age : 'N/A',
                    'Mobile No' => $item->mobile_no,
                    'Rejected Reason' => $item->rejected_reason,
                ];
            }

            return [];
        });
    }

    public function headings(): array
    {
        if ($this->reportType === '2') {
            return ['Application ID', 'Applicant Name', 'Father\'s Name', 'Age'];
        } elseif ($this->reportType === '3') {
            return ['Beneficiary ID', 'Application ID', 'Applicant Name', 'Father\'s Name', 'Age'];
        } elseif ($this->reportType === '1' || $this->reportType === '5') {
            return ['Application ID', 'Applicant Name', 'Father\'s Name', 'Age', 'Mobile No'];
        } elseif ($this->reportType === '4') {
            return ['Application ID', 'Applicant Name', 'Father\'s Name', 'Age', 'Mobile No', 'Rejected Reason'];
        } else {
            return [];
        }
    }
}
