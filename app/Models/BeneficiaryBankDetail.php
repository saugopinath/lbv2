<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class BeneficiaryBankDetail extends BaseAuditableModel
{
    use Searchable;
    protected $table = "pension.beneficiary_banks";
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];
    public function searchableAs()
    {
        return 'pension_beneficiary_banks';
    }
    public function toSearchableArray()
    {
        return [
            'scheme_id' => $this->scheme_id,
            'application_id' => $this->application_id,
            'beneficiary_id' => $this->beneficiary_id,
            'ifscode' => $this->ifscode,
            'bankname' => $this->bankname,
            'bank_branch_name' => $this->bank_branch_name,
            'bankaccountnumber' => $this->bankaccountnumber,
            'other_details' => $this->other_details,
            'is_clean' => $this->is_clean,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    public function personal()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }

    public function bankname()
    {
        $ifsc = $this->ifscbranch;
        $accno = $this->bank_account_number;
        if ($ifsc && $ifsc->bank) {
            return [
                'bank_name' => $ifsc->bank->name,
                'branch_name' => $ifsc->branch,
                'ifsc_code' => $ifsc->code,
                'accno' => $accno,
            ];
        }
    }
}
