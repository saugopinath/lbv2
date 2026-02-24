<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryBankDetail extends BaseAuditableModel
{
    protected $table = "pension.beneficiary_banks";
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];

    public function bankname()
    {
        $ifsc = $this->ifscbranch;
        $accno = $this->bank_account_number;
        if ($ifsc && $ifsc->bank) {
            return [
                'bank_name'   => $ifsc->bank->name,
                'branch_name' => $ifsc->branch,
                'ifsc_code'   => $ifsc->code,
                'accno' => $accno,
            ];
        }
    }
}
