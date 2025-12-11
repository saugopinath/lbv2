<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JNMPExport implements FromCollection, WithHeadings
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * Return a collection of rows for the export.
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * Headings for the exported file.
     */
    public function headings(): array
    {
        return [
            'Application ID',
            'Beneficiary ID',
            'Applicant Name',
            "Father's Name",
            'Date of Birth',
            'Mobile No',
            'Address'
        ];
    }
}
