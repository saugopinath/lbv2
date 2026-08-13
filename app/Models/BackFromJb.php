<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackFromJb extends BaseAuditableModel
{
    protected $table = 'pension.back_from_jbs';
    protected $primaryKey = 'application_id';
    public function beneficiary()
    {
        return $this->hasOne(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }

    public function contact()
    {
        return $this->hasOne(BeneficiaryContactDetail::class, 'application_id', 'application_id');
    }
}
