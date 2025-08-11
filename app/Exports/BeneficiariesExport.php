<?php

namespace App\Exports;

use Carbon\Carbon;
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
        if ($this->reportType === "verified") {
            $query = DraftBeneficiaryPersonal::query()
                ->where("next_level_role_id", 22)
                ->with([
                    'father' => fn($q) => $q->where('relation_type_id', 79),
                ])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', 79));
        }

        if ($this->reportType === "approved") {
            $query = BeneficiaryPersonal::query()
                ->where("next_level_role_id", 23)
                ->with([
                    'father' => fn($q) => $q->where('relation_type_id', 79),
                ])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', 79));
        }

        if ($this->reportType === "reverted") {
            $query = DraftBeneficiaryPersonal::query()
                ->where("next_level_role_id", 21)
                ->with([
                    'father' => fn($q) => $q->where('relation_type_id', 79),
                ])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', 79));
        }

        if ($this->reportType === "partial") {
            $query = DraftBeneficiaryPersonal::query()
                ->where("next_level_role_id", 21)
                ->with([
                    'father' => fn($q) => $q->where('relation_type_id', 79),
                ])
                ->whereHas('father', fn($q) => $q->where('relation_type_id', 79));
        }

        if ($this->reportType === "rejected") {
            $query = BenRejectDetail::query();
        }

       if ($this->loginType === 'district_office' && $this->districtCode) {
            $query->where('district_id', $this->districtCode);
        } elseif ($this->loginType === 'subdivision_office' && $this->subdivisionCode) {
            $query->where('municipality_id', $this->subdivisionCode);
        } elseif ($this->loginType === 'block_office' && $this->blockCode) {
            $query->where('block_id', $this->blockCode);
        }

        $data = $query->get();

        return $data->map(function ($item) {
            if ($this->reportType === 'verified') {
                return [
                    'Application ID' => $item->application_id,
                    'Applicant Name' => $item->full_name,
                    'Father\'s Name' => optional($item->father->first())->full_name ?? 'N/A',
                    'Age' => $item->dob ? Carbon::parse($item->dob)->age : 'N/A',
                ];
            }

            if ($this->reportType === 'approved') {
                return [
                    'Beneficiary ID' => $item->beneficiary_id,
                    'Application ID' => $item->application_id,
                    'Applicant Name' => $item->full_name,
                    'Father\'s Name' => optional($item->father->first())->full_name ?? 'N/A',
                    'Age' => $item->dob ? Carbon::parse($item->dob)->age : 'N/A',
                ];
            }

            if (in_array($this->reportType, ['reverted', 'partial'])) {
                return [
                    'Application ID' => $item->application_id,
                    'Applicant Name' => $item->full_name,
                    'Father\'s Name' => optional($item->father->first())->full_name ?? 'N/A',
                    'Age' => $item->dob ? Carbon::parse($item->dob)->age : 'N/A',
                    'Mobile No' => $item->mobile_no,
                ];
            }

            if ($this->reportType === 'rejected') {
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
        if ($this->reportType === 'verified') {
            return ['Application ID', 'Applicant Name', 'Father\'s Name', 'Age'];
        } elseif ($this->reportType === 'approved') {
            return ['Beneficiary ID', 'Application ID', 'Applicant Name', 'Father\'s Name', 'Age'];
        } elseif ($this->reportType === 'reverted' || $this->reportType === 'partial') {
            return ['Application ID', 'Applicant Name', 'Father\'s Name', 'Age', 'Mobile No'];
        } elseif ($this->reportType === 'rejected') {
            return ['Application ID', 'Applicant Name', 'Father\'s Name', 'Age', 'Mobile No', 'Rejected Reason'];
        } else {
            return [];
        }
    }
}
