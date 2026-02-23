<?php

namespace App\Models;


class BeneficiaryAadhaar extends BaseAuditableModel
{
  protected $guarded = [];
  protected $primaryKey = 'application_id';
  protected $table = 'pension.beneficiary_aadhars';
  public $incrementing = false;
  public function personal()
  {
    return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
  }
}
